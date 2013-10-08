<?php namespace SafeStartApi\Controller\Plugin;

use ZendPdf;
use SafeStartApi\Model\ImageProcessor;

class VehicleReportPdfPlugin extends \SafeStartApi\Controller\Plugin\AbstractPdfPlugin
{
    const HEADER_EMPIRIC_HEIGHT = 90;
    protected $pageSize = ZendPdf\Page::SIZE_A4_LANDSCAPE;
    private $vehicle;

    public function create(\SafeStartApi\Entity\Vehicle $vehicle, \DateTime $from = null, \DateTime $to = null)
    {
        $this->vehicle = $vehicle;
        $this->document = new ZendPdf\PdfDocument();
        $this->opts = $this->getController()->moduleConfig['pdf']['vehicleReport'];
        $this->uploadPath = $this->getController()->moduleConfig['defUsersPath'];
        $fontPath = dirname(__FILE__) . "/../../../../public/fonts/HelveticaNeueLTStd-Cn.ttf";
        $this->font = file_exists($fontPath) ? ZendPdf\Font::fontWithPath($fontPath) : ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_HELVETICA);
        $this->document->pages[$this->pageIndex] = new ZendPdf\Page($this->pageSize);
        // add header
        $this->lastTopPos = $this->drawHeader();
        // add report info
        $this->drawReportInfo($from, $to);
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
        $data = array(
            'Company name' => isset($companyData['title']) ? $companyData['title'] : '',
            'Vehicle title' => $vehicleData['title'],
            'Project number' => $vehicleData['projectNumber'],
            'Project name' => $vehicleData['projectName'],
            'Plant ID' => $vehicleData['plantId'],
            'Registration' => $vehicleData['registration'],
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
