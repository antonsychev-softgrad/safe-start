<?php namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use ZendPdf;
use SafeStartApi\Model\ImageProcessor;

class InspectionFaultPdfPlugin extends \SafeStartApi\Controller\Plugin\AbstractPdfPlugin
{
    const HEADER_EMPIRIC_HEIGHT = 90;
    protected $pageSize = ZendPdf\Page::SIZE_A4_LANDSCAPE;
    private $checkList;

    public function create(\SafeStartApi\Entity\CheckList $checklist)
    {
        $this->checkList = $checklist;
        $this->document = new ZendPdf\PdfDocument();
        $this->opts = $this->getController()->moduleConfig['pdf']['inspection_fault'];
        $this->uploadPath = $this->getController()->moduleConfig['defUsersPath'];
        $fontPath = dirname(__FILE__) . "/../../../../public/fonts/HelveticaNeueLTStd-Cn.ttf";
        $this->font = file_exists($fontPath) ? ZendPdf\Font::fontWithPath($fontPath) : ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_HELVETICA);
        $this->document->pages[$this->pageIndex] = new ZendPdf\Page($this->pageSize);
        // add header
        $this->lastTopPos = $this->drawHeader();
        // add additional comments
        $this->drawAlerts(1);
        // save document
        $this->fileName = $this->getName();
        $this->filePath = $this->getFullPath();
        $this->saveDocument();
        $this->checkList->getFaultPdfLink($this->fileName);
        $this->getController()->em->flush();
        return $this->filePath;
    }


    private function drawHeader()
    {
        $vehicle = $this->checkList->getVehicle();
        $company = $vehicle->getCompany();
        $vehicleData = $vehicle->toInfoArray();
        $companyData = $company ? $company->toArray() : array();
        $data = array(
            'Company name' => isset($companyData['title']) ? $companyData['title'] : '',
            'Vehicle title' => $vehicleData['title'],
            'Project number' => $vehicleData['projectNumber'],
            'Project name' => $vehicleData['projectName'],
            'Plant ID' => $vehicleData['plantId'],
            //'Registration' => $vehicleData['registration'],
            'Type of vehicle' => $vehicleData['type'],
            'Service due' => $vehicleData['serviceDueKm'] . ' km ' . $vehicleData['serviceDueHours'] . ' hours',
            'Current odometer' => $vehicleData['currentOdometerKms'] . ' km ' . $vehicleData['currentOdometerHours'] . ' hours',
            'Next Service Day' => $vehicleData['nextServiceDay'] ? $vehicleData['nextServiceDay'] : '-',
        );

        if (isset($companyData['expiry_date']) && !empty($companyData['expiry_date'])) $data['Expiry Day'] = date($this->getController()->moduleConfig['params']['date_format'], $companyData['expiry_date']);

        $pageHeight = $this->getPageHeight();
        $pageWidth = $this->getPageWidth();
        $contentWidth = $this->getPageContentWidth();

        // draw logo image >
        $root = $this->getRootPath();

        $logoMaxWidth = 130;
        $logoMaxHeight = 115;
        $logoPath = "{$root}/public/logo-pdf.png";

        $logo = ZendPdf\Image::imageWithPath($logoPath);
        $logoWidth = $logo->getPixelWidth();
        $logoHeight = $logo->getPixelHeight();

        $scale = min($logoMaxWidth / $logoWidth, $logoMaxHeight / $logoHeight);
        $logoNewWidth = (int)($logoWidth * $scale);
        $logoNewHeight = (int)($logoHeight * $scale);

        $this->document->pages[$this->pageIndex]->drawImage($logo, $this->opts['style']['page_padding_left'], $pageHeight - 4 - $logoNewHeight, $this->opts['style']['page_padding_left'] + $logoNewWidth, $pageHeight - 4);
        // > end draw logo image.

        $headerTitlePaddingRight = 25;
        $headerTitleXOffset = $logoMaxWidth + $headerTitlePaddingRight;
        // draw header title >

        $text = strtoupper($this->opts['title']);
        $topPosInPage = $this->drawText($text, self::PAGE_HEADER_TITLE_SIZE, '#0F5B8D', $pageHeight - 16, self::TEXT_ALIGN_LEFT, $headerTitleXOffset);

        if (!empty($data['Company name']) && is_string($data['Company name'])) {
            $topPosInPage -= (self::PAGE_HEADER_TITLE_SIZE + (self::BLOCK_TEXT_LINE_SPACING_AT * 2));
            $topPosInPage = $this->drawText($data['Company name'], self::PAGE_HEADER_TITLE_SIZE, '#0F5B8D', $topPosInPage, self::TEXT_ALIGN_LEFT, $headerTitleXOffset);
            unset($data['Company name']);
        }

        $columnsLeftXOffset = 290;
        $columns = 2;
        $columnsPadding = 15;
        $total = count($data);
        $inColumn = ceil($total / $columns);
        $columnWidth = ($contentWidth - $columnsLeftXOffset - ($columnsPadding * ($columns - 1))) / $columns;
        $keyWidth = $columnWidth - 50;
        $currentYPos = $pageHeight - 15;
        for ($i = 0; $i < $inColumn; $i++) {
            $currentXPos = $columnsLeftXOffset;
            for ($c = 0; $c < $columns; $c++) {
                $navIndex = $i + ($inColumn * $c);
                if ($navIndex >= $total) continue;
                $tValue = array_slice($data, $navIndex, 1, true);
                $tKeys = array_keys($tValue);
                $tVals = array_values($tValue);

                $title = $tKeys[0];
                $value = (!empty($tVals[0]) && !is_null($tVals[0])) ? (string)$tVals[0] : '-';

                $title = strip_tags($title);
                $title = ucwords($title);
                $fLinePos = $currentYPos;
                $currentYPos = $this->drawText($title, self::BLOCK_SUBHEADER_SIZE, '#333333', $currentYPos, self::TEXT_ALIGN_LEFT, $currentXPos);

                // draw status >
                $color = "#0f5b8d";
                $value = strtoupper($value);

                $this->drawText($value, self::BLOCK_SUBHEADER_SIZE, $color, $fLinePos, self::TEXT_ALIGN_RIGHT, -(($columnWidth + $columnsPadding) * ($columns - $c - 1)));

                $currentXPos += ($columnWidth + $columnsPadding);
            }
            $currentYPos -= (self::BLOCK_SUBHEADER_SIZE + (self::BLOCK_TEXT_LINE_SPACING_AT * 2));
        }

        // > end draw header line.
        $topPosInPage = $currentYPos;

        return $topPosInPage;
    }

    protected function drawFooter(\ZendPdf\Page $page)
    {
        $maxHeight = $imageMaxHeight = $this->opts['style']['page_padding_bottom'] / 16 * 10;
        $imageMaxWidth = $imageMaxHeight / 3 * 4;
        $topPosInPage = (($maxHeight) / 2);

        $user = $this->checkList->getUser();
        if ($user) $userData = $user->toInfoArray();
        else $userData = (!is_array($this->checkList->getUserData())) ? json_decode((string)$this->checkList->getUserData(), true) : $this->checkList->getUserData();

        $userName = "Name: " . $this->checkList->getOperatorName();
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
            $image = new \SafeStartApi\Model\ImageProcessor($signaturePath);
            $image->cover(array(
                    'width' => $this->opts['style']['signature_width'],
                    'height' => $this->opts['style']['signature_height'],
                    'position' => 'centermiddle',
                )
            );
            $newImagePath = $this->getUploadPath() . $userData['signature'] . "120x60.jpg";
            $image->save($newImagePath);

            $alertImage = ZendPdf\Image::imageWithPath($newImagePath);

            $page->drawImage($alertImage,
                $leftPosInStr + 30,
                $topPosInPage - 10,
                $leftPosInStr + ($this->opts['style']['signature_width'] / 2) + 30,
                $topPosInPage + ($this->opts['style']['signature_height'] / 2) - 10
            );
        }

        $leftPosInStr = $this->getLeftStartPos($date, $this->font, self::BLOCK_TEXT_SIZE, self::TEXT_ALIGN_RIGHT);
        $page->drawText($date, $leftPosInStr, $topPosInPage);

        return true;
    }

    public function drawAlerts($currentColumn)
    {
        $alerts = $this->checkList->getVehicle()->getAlerts();
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

        foreach ($alerts as $alertObg) {
            $alert = $alertObg->toArray();
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
                $image = new \SafeStartApi\Model\ImageProcessor($imagePath);
                $imageWidth = $columnWidth - $columnsPadding;
                $imageHeight = round($imageWidth * (2 / 3));
                $image->cover(array(
                        'width' => $imageWidth,
                        'height' => $imageHeight,
                        'position' => 'centermiddle',
                    )
                );
                $newImagePath = $this->getUploadPath() . $imageHash . $imageWidth . "x" . $imageHeight . ".jpg";
                $image->save($newImagePath);

                $alertImage = ZendPdf\Image::imageWithPath($newImagePath);


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
