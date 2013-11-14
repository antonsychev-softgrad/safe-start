<?php namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use ZendPdf;
use SafeStartApi\Model\ImageProcessor;

class InspectionPdfPlugin extends \SafeStartApi\Controller\Plugin\AbstractPdfPlugin
{
    const HEADER_EMPIRIC_HEIGHT = 100;
    protected $pageSize = ZendPdf\Page::SIZE_A4_LANDSCAPE;
    private $checkList;

    public function create(\SafeStartApi\Entity\CheckList $checklist)
    {
        $this->checkList = $checklist;
        $this->document = new ZendPdf\PdfDocument();
        $this->opts = $this->getController()->moduleConfig['pdf']['inspection'];
        $this->uploadPath = $this->getController()->moduleConfig['defUsersPath'];
        $fontPath = dirname(__FILE__) . "/../../../../public/fonts/HelveticaNeueLTStd-Cn.ttf";
        $this->font = file_exists($fontPath) ? ZendPdf\Font::fontWithPath($fontPath) : ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_HELVETICA);
        $this->document->pages[$this->pageIndex] = new ZendPdf\Page($this->pageSize);
        // add header
        $this->lastTopPos = $this->drawHeader();
        // add warnings
        $currentColumn = $this->drawWarnings();
        // add location
        $currentColumn = $this->drawLocation($currentColumn);
        // add inspection fields
        $currentColumn = $this->drawInspection($currentColumn);
        // add additional comments
        $this->drawAlerts($currentColumn);
        // save document
        $this->fileName = $this->getName();
        $this->filePath = $this->getFullPath();
        $this->saveDocument();
        $this->checkList->setPdfLink($this->fileName);
        $this->getController()->em->flush();
        $cache = \SafeStartApi\Application::getCache();
        $cashKey = $this->fileName;
        $cache->setItem($cashKey, true);

        return $this->filePath;
    }


    protected function drawHeader()
    {
        $vehicle = $this->checkList->getVehicle();
        $company = $vehicle->getCompany();
        $vehicleData = $vehicle->toInfoArray();
        $companyData = $company ? $company->toArray() : array();
        return $this->drawVehicleHeader($vehicleData, $companyData);
    }

    protected function drawFooter(\ZendPdf\Page $page)
    {
        $maxHeight = $imageMaxHeight = $this->opts['style']['page_padding_bottom'] / 16 * 10;
        $imageMaxWidth = $imageMaxHeight / 3 * 4;

        $topPosInPage = (($maxHeight) / 2);

        $userData = array();
        $user = $this->checkList->getUser();
        if ($user) $userData = $user->toInfoArray();
        else $userData = (!is_array($this->checkList->getUserData())) ? json_decode((string)$this->checkList->getUserData(), true) : $this->checkList->getUserData();

        $userName = "Operator Name: " . $this->checkList->getOperatorName();
        $date = "Date: " . ($this->checkList->getCreationDate()->format($this->getController()->moduleConfig['params']['date_format'] . ' ' . $this->getController()->moduleConfig['params']['time_format']));
        $signature = "Signature: ";

        $color = ZendPdf\Color\Html::color($this->opts['style']['footer_text_color']);
        $style = new ZendPdf\Style();
        $style->setFillColor($color);
        $style->setFont($this->font, $this->opts['style']['footer_text_size']);
        $page->setStyle($style);

        $leftPosInStr = $this->getLeftStartPos($userName, $this->font, self::BLOCK_TEXT_SIZE, self::TEXT_ALIGN_LEFT);
        $page->drawText($userName, $leftPosInStr, $topPosInPage);

        $strWidth = $this->widthForStringUsingFontSize($signature, $this->font, self::BLOCK_TEXT_SIZE);
        $leftPosInStr = $this->getLeftStartPos($signature, $this->font, self::BLOCK_TEXT_SIZE, self::TEXT_ALIGN_CENTER) - 20;
        $page->drawText($signature, $leftPosInStr - ($imageMaxWidth / 2), $topPosInPage);

        if (($signaturePath = $this->getImagePathByName(isset($userData['signature']) ? $userData['signature'] : '')) !== null) {
            $signatureImage = ZendPdf\Image::imageWithPath($signaturePath);
            $signatureWidth = $signatureImage->getPixelWidth();
            $signatureHeight = $signatureImage->getPixelHeight();

            $scale = min($this->opts['style']['signature_width'] / $signatureWidth, $this->opts['style']['signature_height'] / $signatureHeight);
            $signatureNewWidth = (int)($signatureWidth * $scale);
            $signatureNewHeight = (int)($signatureHeight * $scale);

            $page->drawImage($signatureImage,
                $leftPosInStr + 30,
                $topPosInPage - 10,
                $leftPosInStr + ($signatureNewWidth / 2) + 30,
                $topPosInPage + ($signatureNewHeight / 2) - 10
            );
        }

        $leftPosInStr = $this->getLeftStartPos($date, $this->font, self::BLOCK_TEXT_SIZE, self::TEXT_ALIGN_RIGHT);
        $page->drawText($date, $leftPosInStr, $topPosInPage);

        return true;
    }

    private function drawWarnings($currentColumn = 1)
    {
        $warnings = $this->checkList->getWarnings();
        $vehicle = $this->checkList->getVehicle();
        if ($vehicle->getNextServiceDay()) {
            $days = (strtotime($vehicle->getNextServiceDay()) - $this->checkList->getCreationDate()->getTimestamp()) / (60 * 60 * 24);
            if ($days < 1) {
                $warnings[] = array(
                    'action' => 'next_service_due',
                    'text' => 'Next service day is ' . $vehicle->getNextServiceDay()
                );
            } else if ($days < 30) {
                $warnings[] = array(
                    'action' => 'next_service_due',
                    'text' => sprintf($this->opts['style']['next_service_due'], ceil($days))
                );
            }
        }
        if ($vehicle->getCompany()) {
            if ($vehicle->getCompany()->getExpiryDate()) {
                $days = ($vehicle->getCompany()->getExpiryDate() - $this->checkList->getCreationDate()->getTimestamp()) / (60 * 60 * 24);
                if ($days < 1) {
                    $warnings[] = array(
                        'action' => 'subscription_ending',
                        'text' => 'Your subscription has expired'
                    );
                } else if ($days < 30) {
                    $warnings[] = array(
                        'action' => 'subscription_ending',
                        'text' => sprintf($this->opts['style']['subscription_ending'], ceil($days))
                    );
                }
            }
        }
        if (empty($warnings)) return $currentColumn;
        $columns = $this->opts['style']['content_columns'];
        $columnsPadding = $this->opts['style']['content_column_padding'];
        $contentWidth = $this->getPageContentWidth() * $this->opts['style']['content_width'];
        $columnWidth = round(($contentWidth - ($columnsPadding * ($columns - 1))) / $columns);
        if (!$currentColumn) $currentColumn = 1;

        foreach ($warnings as $warning) {
            if (!isset($warning['action'])) continue;
            if (isset($this->opts['style'][$warning['action']])) {
                $lines = $this->getTextLines(isset($warning['text']) ? $warning['text'] : $this->opts['style'][$warning['action']], $this->opts['style']['warning_size'], $columnWidth);
                foreach ($lines as $line) {
                    if ($this->lastTopPos <= ($this->opts['style']['page_padding_bottom'] + $this->getPageHeight() * $this->opts['style']['content_height'])) {
                        $currentColumn++;
                        if ($currentColumn > $columns) {
                            $this->pageIndex++;
                            $this->document->pages[$this->pageIndex] = new ZendPdf\Page($this->pageSize);
                            $currentColumn = 1;
                        }
                        $this->lastTopPos = ($this->pageIndex) ? $this->getPageHeight() - $this->opts['style']['page_padding_top'] : $this->getPageHeight() - self::HEADER_EMPIRIC_HEIGHT;
                    }
                    $this->drawText(
                        $line,
                        $this->opts['style']['warning_size'],
                        $this->opts['style']['warning_color'],
                        $this->lastTopPos,
                        self::TEXT_ALIGN_CENTER,
                        ($this->opts['style']['column_padding_left'] + ($currentColumn - 1) * $columnWidth),
                        $this->font,
                        $columnWidth
                    );

                    $this->lastTopPos -= ($this->opts['style']['warning_size'] + ($this->opts['style']['warning_line_spacing'] * 2));
                }
            }
        }

        $this->lastTopPos -= 10;

        return $currentColumn;
    }

    private function drawLocation($currentColumn = 1)
    {
        $columns = $this->opts['style']['content_columns'];
        $columnsPadding = $this->opts['style']['content_column_padding'];
        $contentWidth = $this->getPageContentWidth() * $this->opts['style']['content_width'];
        $columnWidth = round(($contentWidth - ($columnsPadding * ($columns - 1))) / $columns);
        if (!$currentColumn) $currentColumn = 1;

        $location = $this->checkList->getLocation();
        $gps = $this->checkList->getGpsCoords();
        if (!empty($location) || !empty($gps)) {
            $this->drawText(
                "Location",
                $this->opts['style']['category_field_size'],
                $this->opts['style']['category_field_color'],
                $this->lastTopPos,
                self::TEXT_ALIGN_CENTER,
                ($this->opts['style']['column_padding_left'] + ($currentColumn - 1) * $columnWidth),
                $this->font,
                $columnWidth
            );
            $this->lastTopPos -= 10;

            $lines = array_filter($this->getTextLines($location, $this->opts['style']['field_size'], $columnWidth));
            foreach ((array)$lines as $line) {
                if (empty($line)) continue;
                if ($this->lastTopPos <= ($this->opts['style']['page_padding_bottom'] + $this->getPageHeight() * $this->opts['style']['content_height'])) {
                    $currentColumn++;
                    if ($currentColumn > $columns) {
                        $this->pageIndex++;
                        $this->document->pages[$this->pageIndex] = new ZendPdf\Page($this->pageSize);
                        $currentColumn = 1;
                    }
                    $this->lastTopPos = ($this->pageIndex) ? $this->getPageHeight() - $this->opts['style']['page_padding_top'] : $this->getPageHeight() - self::HEADER_EMPIRIC_HEIGHT;
                }
                $this->drawText(
                    $line,
                    $this->opts['style']['field_size'],
                    $this->opts['style']['field_color'],
                    $this->lastTopPos,
                    self::TEXT_ALIGN_LEFT,
                    ($this->opts['style']['column_padding_left'] + ($currentColumn - 1) * $columnWidth) + $columnsPadding / 2,
                    $this->font,
                    $columnWidth
                );
                $this->lastTopPos -= ($this->opts['style']['field_size'] + ($this->opts['style']['field_line_spacing'] * 2));
            }

            if (!empty($gps)) {
                $mapUrl = "http://maps.googleapis.com/maps/api/staticmap?center=" . str_replace(";", ",", $gps) . "&zoom=12&size=400x400&markers=color:blue|53.9128749,27.5135608&sensor=false";
                $moduleConfig = $this->getController()->getServiceLocator()->get('Config');
                $mapPath =  dirname(__FILE__) . "/../../../../../.." . $moduleConfig['defUsersPath'] . uniqid() . ".png";
                file_put_contents($mapPath, file_get_contents($mapUrl));

                if (file_exists($mapPath)) {
                    $imageWidth = $columnWidth;
                    $imageHeight = $imageWidth;

                    $alertImage = ZendPdf\Image::imageWithPath($mapPath);
                    $alertImageWidth = $alertImage->getPixelWidth();
                    $alertImageHeight = $alertImage->getPixelHeight();

                    $scale = min($imageWidth / $alertImageWidth, $imageHeight / $alertImageHeight);
                    $imageWidth = (int)($alertImageWidth * $scale);
                    $imageHeight = (int)($alertImageHeight * $scale);

                    $this->document->pages[$this->pageIndex]->drawImage(
                        $alertImage,
                        ($this->opts['style']['page_padding_left'] + ($currentColumn - 1) * $columnWidth) ,
                        $this->lastTopPos - $imageHeight,
                        ($this->opts['style']['page_padding_left'] + ($currentColumn - 1) * $columnWidth) + $imageWidth,
                        $this->lastTopPos
                    );

                    $this->lastTopPos = $this->lastTopPos - $imageHeight - 10;
                }

            }

            $this->lastTopPos -= 10;
        }
    }

    private function drawInspection($currentColumn = 1)
    {
        $fieldsStructure = json_decode($this->checkList->getFieldsStructure());
        $fieldsData = json_decode($this->checkList->getFieldsData(), true);
        $fieldsDataValues = array();
        foreach ($fieldsData as $fieldData) $fieldsDataValues[$fieldData['id']] = $fieldData['value'];

        $columns = $this->opts['style']['content_columns'];
        $columnsPadding = $this->opts['style']['content_column_padding'];
        $contentWidth = $this->getPageContentWidth() * $this->opts['style']['content_width'];
        $columnWidth = round(($contentWidth - ($columnsPadding * ($columns - 1))) / $columns);
        if (!$currentColumn) $currentColumn = 1;

        foreach ($fieldsStructure as $groupBlock) {
            if ($this->isEmptyGroup($groupBlock, $fieldsDataValues)) continue;
            $text = (isset($groupBlock->fieldDescription) && !empty($groupBlock->fieldDescription)) ? $groupBlock->fieldDescription : $groupBlock->groupName;
            $lines = $this->getTextLines($text, $this->opts['style']['category_field_size'], $columnWidth);
            foreach ($lines as $line) {
                if ($this->lastTopPos <= ($this->opts['style']['page_padding_bottom'] + $this->getPageHeight() * $this->opts['style']['content_height'])) {
                    $currentColumn++;
                    if ($currentColumn > $columns) {
                        $this->pageIndex++;
                        $this->document->pages[$this->pageIndex] = new ZendPdf\Page($this->pageSize);
                        $currentColumn = 1;
                    }
                    $this->lastTopPos = ($this->pageIndex) ? $this->getPageHeight() - $this->opts['style']['page_padding_top'] : $this->getPageHeight() - self::HEADER_EMPIRIC_HEIGHT;
                }
                $this->drawText(
                    $line,
                    $this->opts['style']['category_field_size'],
                    $this->opts['style']['category_field_color'],
                    $this->lastTopPos,
                    self::TEXT_ALIGN_CENTER,
                    ($this->opts['style']['column_padding_left'] + ($currentColumn - 1) * $columnWidth),
                    $this->font,
                    $columnWidth
                );

                $this->lastTopPos -= ($this->opts['style']['category_field_size'] + ($this->opts['style']['category_field_line_spacing'] * 2));

                if (isset($groupBlock->fields)) $currentColumn = $this->drawInspectionFields($groupBlock->fields, $fieldsDataValues, $currentColumn);

                $this->lastTopPos -= 10;
            }

        }

        return $currentColumn;
    }

    private function drawInspectionFields($fields, $fieldsDataValues, $currentColumn = 1)
    {
        $columns = $this->opts['style']['content_columns'];
        $columnsPadding = $this->opts['style']['content_column_padding'];
        $contentWidth = $this->getPageContentWidth() * $this->opts['style']['content_width'];
        $columnWidth = round(($contentWidth - ($columnsPadding * ($columns - 1))) / $columns);
        $columnFieldValueWidth = 50;
        $columnFieldTitleWidth = $columnWidth - $columnFieldValueWidth;

        foreach ($fields as $field) {
            // todo: check if additional field if triggered
            if ($field->type == 'label') continue;
            $text = (isset($field->fieldDescription) && !empty($field->fieldDescription)) ? $field->fieldDescription : (string)$field->fieldName;
            $lines = array_filter($this->getTextLines($text, $this->opts['style']['field_size'], ($field->type == 'group') ? $columnWidth : $columnFieldTitleWidth));
            $startYPos = $this->lastTopPos;
            foreach ($lines as $line) {
                if ($this->lastTopPos <= ($this->opts['style']['page_padding_bottom'] + $this->getPageHeight() * $this->opts['style']['content_height'])) {
                    $currentColumn++;
                    if ($currentColumn > $columns) {
                        $this->pageIndex++;
                        $this->document->pages[$this->pageIndex] = new ZendPdf\Page($this->pageSize);
                        $currentColumn = 1;
                    }
                    $this->lastTopPos = ($this->pageIndex) ? $this->getPageHeight() - $this->opts['style']['page_padding_top'] : $this->getPageHeight() - self::HEADER_EMPIRIC_HEIGHT;
                    $startYPos = $this->lastTopPos;
                }
                $this->drawText(
                    $line,
                    $this->opts['style']['field_size'],
                    ($field->type == 'group') ? $this->opts['style']['field_group_color'] : $this->opts['style']['field_color'],
                    $this->lastTopPos,
                    ($field->type == 'group') ? self::TEXT_ALIGN_CENTER : self::TEXT_ALIGN_LEFT,
                    ($this->opts['style']['column_padding_left'] + ($currentColumn - 1) * $columnWidth) + $columnsPadding / 2,
                    $this->font,
                    ($field->type == 'group') ? $columnWidth : $columnFieldTitleWidth
                );
                $this->lastTopPos -= ($this->opts['style']['field_size'] + ($this->opts['style']['field_line_spacing'] * 2));
            }
            if ($field->type != 'group') {
                if ($field->type == 'datePicker' && isset($fieldsDataValues[$field->id]) && !empty($fieldsDataValues[$field->id])) {
                    $value = !empty($fieldsDataValues[$field->id]) ? date($this->getController()->moduleConfig['params']['date_format'], (int)$fieldsDataValues[$field->id]) : '-';
                } else {
                    $value = (isset($fieldsDataValues[$field->id]) && !empty($fieldsDataValues[$field->id])) ? $fieldsDataValues[$field->id] : '-';
                }
                if (!$field->additional && (strtolower($field->triggerValue) == strtolower($value))) {
                    $value = (isset($fieldsDataValues[$field->id]) && !empty($fieldsDataValues[$field->id])) ? $fieldsDataValues[$field->id] : '-';
                    //$value = $this->opts['style']['field_alert_text'];
                    $color = $this->opts['style']['field_alert_color'];
                } else {
                    $color = $this->opts['style']['field_ok_color'];
                }
                $value = strtoupper($value);
                $this->drawText(
                    substr($value, 0, 12),
                    $this->opts['style']['field_size'],
                    $color,
                    ($startYPos - (count($lines) - 1) * (($this->opts['style']['field_size'] + ($this->opts['style']['field_line_spacing'] * 2)) / 2)),
                    self::TEXT_ALIGN_RIGHT,
                    ($this->opts['style']['column_padding_left'] + ($currentColumn - 1) * $columnWidth) + ($columnFieldTitleWidth),
                    $this->font,
                    $columnFieldValueWidth
                );
            }
            if (!empty($field->items)) $currentColumn = $this->drawInspectionFields($field->items, $fieldsDataValues, $currentColumn);
        }
        return $currentColumn;
    }

    protected function drawAlerts($currentColumn)
    {
        $this->lastTopPos -= 10;
        $alerts = $this->checkList->getAlertsArray();
        if (empty($alerts)) return;
        $columns = $this->opts['style']['content_columns'];
        $columnsPadding = $this->opts['style']['content_column_padding'];
        $contentWidth = $this->getPageContentWidth() * $this->opts['style']['content_width'];
        $columnWidth = round(($contentWidth - ($columnsPadding * ($columns - 1))) / $columns);

        $lines = $this->getTextLines($this->opts['style']['alerts_header'], $this->opts['style']['category_field_size'], $columnWidth);
        foreach ($lines as $line) {
            if ($this->lastTopPos <= ($this->opts['style']['page_padding_bottom'] + $this->getPageHeight() * $this->opts['style']['content_height'])) {
                $currentColumn++;
                if ($currentColumn > $columns) {
                    $this->pageIndex++;
                    $this->document->pages[$this->pageIndex] = new ZendPdf\Page($this->pageSize);
                    $currentColumn = 1;
                }
                $this->lastTopPos = ($this->pageIndex) ? $this->getPageHeight() - $this->opts['style']['page_padding_top'] : $this->getPageHeight() - self::HEADER_EMPIRIC_HEIGHT;
            }
            $this->drawText(
                $line,
                $this->opts['style']['category_field_size'],
                $this->opts['style']['category_field_color'],
                $this->lastTopPos,
                self::TEXT_ALIGN_CENTER,
                ($this->opts['style']['column_padding_left'] + ($currentColumn - 1) * $columnWidth),
                $this->font,
                $columnWidth
            );

            $this->lastTopPos -= ($this->opts['style']['category_field_size'] + ($this->opts['style']['category_field_line_spacing'] * 2));

            $this->lastTopPos -= 10;
        }

        foreach ($alerts as $alert) {
            if ($alert['status'] == \SafeStartApi\Entity\Alert::STATUS_CLOSED) continue;
            // Description
            $text = !empty($alert['field']['alert_description']) ? $alert['field']['alert_description'] : $alert['field']['alert_title'];
            $lines = $this->getTextLines($text, $this->opts['style']['alert_description_size'], $columnWidth);
            foreach ($lines as $line) {
                if ($this->lastTopPos <= ($this->opts['style']['page_padding_bottom'] + $this->getPageHeight() * $this->opts['style']['content_height'])) {
                    $currentColumn++;
                    if ($currentColumn > $columns) {
                        $this->pageIndex++;
                        $this->document->pages[$this->pageIndex] = new ZendPdf\Page($this->pageSize);
                        $currentColumn = 1;
                    }
                    $this->lastTopPos = ($this->pageIndex) ? $this->getPageHeight() - $this->opts['style']['page_padding_top'] : $this->getPageHeight() - self::HEADER_EMPIRIC_HEIGHT;
                }
                $this->drawText(
                    $line,
                    $this->opts['style']['alert_description_size'],
                    $this->opts['style']['alert_description_color'],
                    $this->lastTopPos,
                    self::TEXT_ALIGN_CENTER,
                    ($this->opts['style']['column_padding_left'] + ($currentColumn - 1) * $columnWidth) + $columnsPadding / 2,
                    $this->font,
                    $columnWidth
                );
                $this->lastTopPos -= ($this->opts['style']['field_size'] + ($this->opts['style']['field_line_spacing'] * 2));
            }

            if (empty($alert['images']) && empty($alert['description']) && empty($alert['comments'])) continue;

            // Additional Comments
            $text = $this->opts['style']['alerts_comments_header'];
            $lines = $this->getTextLines($text, $this->opts['style']['alert_description_size'], $columnWidth);
            foreach ($lines as $line) {
                if ($this->lastTopPos <= ($this->opts['style']['page_padding_bottom'] + $this->getPageHeight() * $this->opts['style']['content_height'])) {
                    $currentColumn++;
                    if ($currentColumn > $columns) {
                        $this->pageIndex++;
                        $this->document->pages[$this->pageIndex] = new ZendPdf\Page($this->pageSize);
                        $currentColumn = 1;
                    }
                    $this->lastTopPos = ($this->pageIndex) ? $this->getPageHeight() - $this->opts['style']['page_padding_top'] : $this->getPageHeight() - self::HEADER_EMPIRIC_HEIGHT;
                }
                $this->drawText(
                    $line,
                    $this->opts['style']['alert_comment_size'],
                    $this->opts['style']['field_group_color'],
                    $this->lastTopPos,
                    self::TEXT_ALIGN_CENTER,
                    ($this->opts['style']['column_padding_left'] + ($currentColumn - 1) * $columnWidth) + $columnsPadding / 2,
                    $this->font,
                    $columnWidth
                );
                $this->lastTopPos -= ($this->opts['style']['field_size'] + ($this->opts['style']['field_line_spacing'] * 2));
            }

            // Comments
            $textArray = array();
            if (!empty($alert['description'])) $textArray[] = $alert['description'];
            if (!empty($alert['comments'])) {
                foreach ($alert['comments'] as $comment) {
                    $textArray[] = $comment['content'];
                }
            }
            $text = implode('; ', $textArray);
            $lines = $this->getTextLines($text, $this->opts['style']['alert_description_size'], $columnWidth);
            foreach ($lines as $line) {
                if ($this->lastTopPos <= ($this->opts['style']['page_padding_bottom'] + $this->getPageHeight() * $this->opts['style']['content_height'])) {
                    $currentColumn++;
                    if ($currentColumn > $columns) {
                        $this->pageIndex++;
                        $this->document->pages[$this->pageIndex] = new ZendPdf\Page($this->pageSize);
                        $currentColumn = 1;
                    }
                    $this->lastTopPos = ($this->pageIndex) ? $this->getPageHeight() - $this->opts['style']['page_padding_top'] : $this->getPageHeight() - self::HEADER_EMPIRIC_HEIGHT;
                }
                $this->drawText(
                    $line,
                    $this->opts['style']['alert_comment_size'],
                    $this->opts['style']['alert_comment_color'],
                    $this->lastTopPos,
                    self::TEXT_ALIGN_LEFT,
                    ($this->opts['style']['column_padding_left'] + ($currentColumn - 1) * $columnWidth) + $columnsPadding / 2,
                    $this->font,
                    $columnWidth
                );
                $this->lastTopPos -= ($this->opts['style']['field_size'] + ($this->opts['style']['field_line_spacing'] * 2));
            }

            if (empty($alert['images'])) continue;
            foreach ($alert['images'] as $imageHash) {
                $imagePath = $this->getImagePathByName($imageHash);
                if (!file_exists($imagePath)) continue;
                $imageWidth = $columnWidth - $columnsPadding;
                $imageHeight = round($imageWidth * (2 / 3));

                $alertImage = ZendPdf\Image::imageWithPath($imagePath);
                $alertImageWidth = $alertImage->getPixelWidth();
                $alertImageHeight = $alertImage->getPixelHeight();

                $scale = min($imageWidth / $alertImageWidth, $imageHeight / $alertImageHeight);
                $imageWidth = (int)($alertImageWidth * $scale);
                $imageHeight = (int)($alertImageHeight * $scale);

                if (($this->lastTopPos - $imageHeight) <= ($this->opts['style']['page_padding_bottom'] + $this->getPageHeight() * $this->opts['style']['content_height'])) {
                    $currentColumn++;
                    if ($currentColumn > $columns) {
                        $this->pageIndex++;
                        $this->document->pages[$this->pageIndex] = new ZendPdf\Page($this->pageSize);
                        $currentColumn = 1;
                    }
                    $this->lastTopPos = ($this->pageIndex) ? $this->getPageHeight() - $this->opts['style']['page_padding_top'] : $this->getPageHeight() - self::HEADER_EMPIRIC_HEIGHT;
                }


                $this->document->pages[$this->pageIndex]->drawImage(
                    $alertImage,
                    ($this->opts['style']['page_padding_left'] + ($currentColumn - 1) * $columnWidth) + $columnsPadding - $columnsPadding / 4,
                    $this->lastTopPos - $imageHeight,
                    ($this->opts['style']['page_padding_left'] + ($currentColumn - 1) * $columnWidth) + $columnsPadding + $imageWidth - $columnsPadding / 4,
                    $this->lastTopPos
                );

                $this->lastTopPos = $this->lastTopPos - round($columnWidth * (2 / 3));
                $this->lastTopPos -= 10;
            }

            $this->lastTopPos -= 10;
        }

    }

    private function isEmptyGroup($group, $fieldsDataValues)
    {
        if (isset($group->items) && is_array($group->items)) {
            $fields = $group->items;
        } elseif (isset($group->fields) && is_array($group->fields)) {
            $fields = $group->fields;
        } else {
            return true;
        }
        foreach ($fields as $field) {
            if ($field->type == 'group') {
                if (!$this->isEmptyGroup($field, $fieldsDataValues)) return false;
            }
            if (!empty($fieldsDataValues[$field->id])) {
                return false;
            }
        }
        return true;
    }

    protected function getName()
    {
        $name = $this->opts['output_name_title'];
        $ext = !empty($this->opts['ext']) ? $this->opts['ext'] : 'pdf';

        $checkList = "0";
        $user = "0";
        $vehicle = "0";

        $date = $this->checkList->getCreationDate()->format('Y-m-d');

        if (!empty($this->checkList)) {
            $checkList = $this->checkList->getId();
            if (($clUser = $this->checkList->getUser()) !== null) {
                $user = $clUser->getId();
            }
            if (($clVehicle = $this->checkList->getVehicle()) !== null) {
                $vehicle = $clVehicle->getId();
            }
        }

        $templateFormat = "{%s}";
        $template = $this->opts['output_name_format'];
        preg_match_all("/" . sprintf($templateFormat, "(.*)") . "/isU", $template, $matches);
        if (!empty($matches[0])) {
            if (!empty($matches[1])) {
                foreach ($matches[1] as $param) {
                    if (!empty($$param)) {
                        $template = preg_replace("/" . sprintf($templateFormat, $param) . "/isU", $$param, $template);
                    } else {
                        $template = preg_replace("/" . sprintf($templateFormat, $param) . "/isU", "", $template);
                    }
                }
            }
        }

        return $template . '.' . $ext;
    }
}
