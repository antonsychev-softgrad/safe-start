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
        return $this->drawVehicleHeader($vehicleData, $companyData);
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
