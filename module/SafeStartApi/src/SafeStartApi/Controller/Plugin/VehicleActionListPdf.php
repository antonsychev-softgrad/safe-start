<?php namespace SafeStartApi\Controller\Plugin;

use ZendPdf;
use SafeStartApi\Model\ImageProcessor;

class VehicleActionListPdf extends \SafeStartApi\Controller\Plugin\AbstractPdfPlugin
{
    const HEADER_EMPIRIC_HEIGHT = 90;
    protected $pageSize = ZendPdf\Page::SIZE_A4_LANDSCAPE;
    private $vehicle;

    public function create($vehicles)
    {
        $this->document = new ZendPdf\PdfDocument();
        $this->opts = $this->getController()->moduleConfig['pdf']['vehicleActionList'];
        $this->uploadPath = $this->getController()->moduleConfig['defUsersPath'];
        $fontPath = dirname(__FILE__) . "/../../../../public/fonts/HelveticaNeueLTStd-Cn.ttf";
        $this->font = file_exists($fontPath) ? ZendPdf\Font::fontWithPath($fontPath) : ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_HELVETICA);
        $this->pageIndex = -1;
        foreach ($vehicles as $vehicle) {
            if (!($vehicle instanceof \SafeStartApi\Entity\Vehicle)) continue;
            if ($this->pageIndex < 0) $this->pageIndex = 0;
            else $this->pageIndex++;
            $this->document->pages[$this->pageIndex] = new ZendPdf\Page($this->pageSize);
            $this->vehicle = $vehicle;
            $this->lastTopPos = $this->drawHeader();
            $currentColumn = $this->drawWarnings();
            $currentColumn = $this->drawAlerts($currentColumn);
        }
        // save document
        $this->fileName = $this->getName();
        $this->filePath = $this->getFullPath();
        $this->saveDocument();
        return array(
            'path' => $this->filePath,
            'name' => $this->fileName
        );
    }


    private function drawHeader()
    {
        $company = $this->vehicle->getCompany();
        $vehicleData = $this->vehicle->toInfoArray();
        $companyData = $company ? $company->toArray() : array();
        return $this->drawVehicleHeader($vehicleData, $companyData);
    }

    private function drawWarnings($currentColumn = 1)
    {

        $vehicle = $this->vehicle;
        $warnings = array();
        if ($this->vehicle->getLastInspection()) {
            $warnings = $this->vehicle->getLastInspection()->getWarnings();
        }

        if ($vehicle->getCompany()) {
            if ($vehicle->getCompany()->getExpiryDate()) {
                $days = ($vehicle->getCompany()->getExpiryDate() - time()) / (60 * 60 * 24);
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

        if ($vehicle->getNextServiceDay()) {
            $days = (strtotime($vehicle->getNextServiceDay()) - time()) / (60 * 60 * 24);
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

        if (empty($warnings)) return $currentColumn;

        $columns = $this->opts['style']['content_columns'];
        $columnsPadding = $this->opts['style']['content_column_padding'];
        $contentWidth = $this->getPageContentWidth() * $this->opts['style']['content_width'];
        $columnWidth = round(($contentWidth - ($columnsPadding * ($columns - 1))) / $columns);
        if (!$currentColumn) $currentColumn = 1;

        $this->drawText(
            $this->opts['style']['warnings_header'],
            $this->opts['style']['category_field_size'],
            $this->opts['style']['category_field_color'],
            $this->lastTopPos,
            self::TEXT_ALIGN_CENTER,
            ($this->opts['style']['column_padding_left'] + ($currentColumn - 1) * $columnWidth),
            $this->font,
            $columnWidth
        );

        $this->lastTopPos -= ($this->opts['style']['category_field_size'] + ($this->opts['style']['category_field_line_spacing'] * 2));

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
                        self::TEXT_ALIGN_LEFT,
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

    protected function drawAlerts($currentColumn)
    {
        $this->lastTopPos -= 10;
        $alerts = $this->vehicle->getAlerts();
        if (empty($alerts)) return;
        $columns = $this->opts['style']['content_columns'];
        $columnsPadding = $this->opts['style']['content_column_padding'];
        $contentWidth = $this->getPageContentWidth() * $this->opts['style']['content_width'];
        $columnWidth = round(($contentWidth - ($columnsPadding * ($columns - 1))) / $columns);

        $this->drawText(
            'Critical Alerts',
            $this->opts['style']['category_field_size'],
            $this->opts['style']['category_field_color'],
            $this->lastTopPos,
            self::TEXT_ALIGN_CENTER,
            ($this->opts['style']['column_padding_left'] + ($currentColumn - 1) * $columnWidth),
            $this->font,
            $columnWidth
        );

        $this->lastTopPos -= ($this->opts['style']['category_field_size'] + ($this->opts['style']['category_field_line_spacing'] * 2));

        foreach ($alerts as $alertObj) {
            $alert = $alertObj->toArray();
            if ($alert['status'] == \SafeStartApi\Entity\Alert::STATUS_CLOSED || !$alert['field']['alert_critical']) continue;
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
                    self::TEXT_ALIGN_LEFT,
                    ($this->opts['style']['column_padding_left'] + ($currentColumn - 1) * $columnWidth),
                    $this->font,
                    $columnWidth
                );
                $this->lastTopPos -= ($this->opts['style']['field_size'] + ($this->opts['style']['field_line_spacing'] * 2));
            }
        }

        $this->lastTopPos -= 10;

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
            'Non-Critical Alerts',
            $this->opts['style']['category_field_size'],
            $this->opts['style']['category_field_color'],
            $this->lastTopPos,
            self::TEXT_ALIGN_CENTER,
            ($this->opts['style']['column_padding_left'] + ($currentColumn - 1) * $columnWidth),
            $this->font,
            $columnWidth
        );

        $this->lastTopPos -= ($this->opts['style']['category_field_size'] + ($this->opts['style']['category_field_line_spacing'] * 2));

        foreach ($alerts as $alertObj) {
            $alert = $alertObj->toArray();
            if ($alert['status'] == \SafeStartApi\Entity\Alert::STATUS_CLOSED || $alert['field']['alert_critical']) continue;
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
                    self::TEXT_ALIGN_LEFT,
                    ($this->opts['style']['column_padding_left'] + ($currentColumn - 1) * $columnWidth),
                    $this->font,
                    $columnWidth
                );
                $this->lastTopPos -= ($this->opts['style']['field_size'] + ($this->opts['style']['field_line_spacing'] * 2));
            }
        }

    }

    protected function drawFooter(\ZendPdf\Page $page)
    {
        $maxHeight = $imageMaxHeight = $this->opts['style']['page_padding_bottom'] / 16 * 10;
        $imageMaxWidth = $imageMaxHeight / 3 * 4;

        $topPosInPage = (($maxHeight) / 2);

        $userData = array();
        $user = \SafeStartApi\Application::getCurrentUser();
        if ($user) $userData = $user->toInfoArray();

        $currentDate = new \DateTime();
        $userName = "Name: " . ((isset($userData['firstName']) ? $userData['firstName'] : '') . " " . (isset($userData['lastName']) ? $userData['lastName'] : ''));
        $date = "Date: " . ($currentDate->format($this->getController()->moduleConfig['params']['date_format'] . ' ' . $this->getController()->moduleConfig['params']['time_format']));
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

    private function drawReportInfo($from, $to)
    {
        $this->lastTopPos -= 10;
        $statistic = $this->vehicle->getStatistic($from, $to);

        $line = "Period from ";
        $this->driveReportInfoLine($line);
        $lineWidth = $this->opts['style']['page_padding_left'] + $this->widthForStringUsingFontSize($line, $this->font, $this->opts['style']['field_size']);
        $newLine = $from->format($this->getController()->moduleConfig['params']['date_format']);
        $line = $line . $newLine . " ";
        $this->driveReportInfoLine($newLine, $lineWidth, $this->opts['style']['field_value_color']);
        $lineWidth = $this->opts['style']['page_padding_left'] + $this->widthForStringUsingFontSize($line, $this->font, $this->opts['style']['field_size']);
        $newLine = "to ";
        $line = $line . $newLine;
        $this->driveReportInfoLine($newLine, $lineWidth);
        $lineWidth = $this->opts['style']['page_padding_left'] + $this->widthForStringUsingFontSize($line, $this->font, $this->opts['style']['field_size']);
        $newLine = $to->format($this->getController()->moduleConfig['params']['date_format']);
        $this->driveReportInfoLine($newLine, $lineWidth, $this->opts['style']['field_value_color']);

        $this->lastTopPos -= ($this->opts['style']['field_size'] + ($this->opts['style']['field_line_spacing'] * 2));

        $this->driveReportInfoLineItem("Amount of travelled kms", $statistic['kms']);
        $this->driveReportInfoLineItem("Sum of used hours", $statistic['hours']);
        $this->driveReportInfoLineItem("Total number of completed inspections", $statistic['inspections']);
        $this->driveReportInfoLineItem("Total number of completed Alerts", $statistic['completed_alerts']);
        $this->driveReportInfoLineItem("Total number of outstanding Alerts", $statistic['new_alerts']);
    }

    private function driveReportInfoLineItem($text, $value)
    {
        $line = $text . ": ";
        $this->driveReportInfoLine($line);
        $lineWidth = $this->opts['style']['page_padding_left'] + $this->widthForStringUsingFontSize($line, $this->font, $this->opts['style']['field_size']);
        $newLine = $value;
        $this->driveReportInfoLine($newLine, $lineWidth, $this->opts['style']['field_value_color']);
        $this->lastTopPos -= ($this->opts['style']['field_size'] + ($this->opts['style']['field_line_spacing'] * 2));
    }

    private function driveReportInfoLine($line = '', $x = null, $color = null)
    {
        if (!$x) $x = $this->opts['style']['page_padding_left'];
        if (!$color) $color = $this->opts['style']['field_color'];
        $this->drawText(
            $line,
            $this->opts['style']['field_size'],
            $color,
            $this->lastTopPos,
            self::TEXT_ALIGN_LEFT,
            $x,
            $this->font,
            $this->getPageContentWidth()
        );
    }

    protected function getName()
    {
        $name = $this->opts['output_name_title'];
        $ext = !empty($this->opts['ext']) ? $this->opts['ext'] : 'pdf';
        $currentDate = new \DateTime();
        $date = $currentDate->format('Y-m-d');

        $user = \SafeStartApi\Application::getCurrentUser()->getId();
        $vehicle = $this->vehicle->getId();

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
