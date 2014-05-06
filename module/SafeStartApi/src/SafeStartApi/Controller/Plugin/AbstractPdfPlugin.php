<?php namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use ZendPdf;
use SafeStartApi\Model\ImageProcessor;

class AbstractPdfPlugin extends AbstractPlugin
{
    const PAGE_HEADER_TITLE_SIZE = 12;
    const PAGE_SUB_HEADER_TITLE_SIZE = 8;

    const BLOCK_SUBHEADER_SIZE = 10;

    const BLOCK_TEXT_SIZE = 12;
    const BLOCK_TEXT_LINE_SPACING_AT = 2;

    const TEXT_ALIGN_LEFT = 'left';
    const TEXT_ALIGN_RIGHT = 'right';
    const TEXT_ALIGN_CENTER = 'center';
    const TEXT_ALIGN_JUSTIFY = 'justify';

    protected $document;
    protected $currentPage;
    protected $pageSize = ZendPdf\Page::SIZE_A4_LANDSCAPE;
    protected $font;
    protected $lastTopPos = 0;
    protected $pageIndex = 0;
    protected $opts = array();
    protected $fileName;
    protected $filePath;


    public function getFilePathByName($name = '')
    {
        return $this->getPdfPath() . $name;
    }

    protected function saveDocument()
    {
        foreach ($this->document->pages as $page) {
            $this->drawFooter($page);
        }
        $this->document->save($this->filePath);
        chmod($this->filePath, 0777);
    }

    protected function drawVehicleHeader($vehicleData = array(), $companyData = array())
    {
        if ($this->checkList && !$this->checkList->getEmailMode() && isset($companyData['description'])) {
            $this->opts['style']['content_height'] = 0;
        }

        $currentOdometer = $vehicleData['currentOdometerKms'] . ' km';
        if ($vehicleData['currentOdometerHours'] > 0) {
            $currentOdometer .=  ' ' . $vehicleData['currentOdometerHours'] . ' hours';
        }
        $data = array(
            'Company name' => isset($companyData['title']) ? $companyData['title'] : 'Safe Start',
            'Vehicle Make' => $vehicleData['title'],
            'Vehicle Model' => $vehicleData['type'],
            'Plant ID' => $vehicleData['plantId'],
            'Project number' => $vehicleData['projectNumber'],
            'Project name' => $vehicleData['projectName'],
            'Service due' => $vehicleData['serviceDueKm'] . ' km ' . $vehicleData['serviceDueHours'] . ' hours',
            'Current odometer' => $currentOdometer,
          //  'Estimated Date of Next Service' => $vehicleData['nextServiceDay'] ? $vehicleData['nextServiceDay'] : '-',
        );

        if ($vehicleData['nextServiceDay']) {
            $data['Estimated Date of Next Service'] = $vehicleData['nextServiceDay'];
        } else if (! $this->checkList->getEmailMode()) {
            $data['Estimated Date of Next Service'] = '-';
        }

        if (isset($companyData['expiry_date']) && !empty($companyData['expiry_date'])) $data['Expiry Day'] = date($this->getController()->moduleConfig['params']['date_format'], $companyData['expiry_date']);
        else if ($this->checkList && ! $this->checkList->getEmailMode()) {
            $data['Expiry Day'] = '-';
        }

        $pageHeight = $this->getPageHeight();
        $pageWidth = $this->getPageWidth();
        $contentWidth = $this->getPageContentWidth();

        $logoMaxWidth = 140;
        $logoMaxHeight = 80;
        $logoPath = '';
        if (isset($companyData['logo'])) {
            $searchDir = $this->getController()->moduleConfig['defUsersPath'];
            $logoPath = \SafeStartApi\Application::getImageFileByDirAndName($this->getRootPath() . $searchDir, $companyData['logo']);
         /*   if ($filePath) {
                $filePathLogo = \SafeStartApi\Application::getImageFileByDirAndName($this->getRootPath() . $searchDir, $companyData['logo'] .'.'. $logoMaxWidth .'x'. $logoMaxHeight);
                if (!$filePathLogo) {
                    $image = new \SafeStartApi\Model\ImageProcessor($filePath);
                    $image->resize(array(
                            'width' => $logoMaxWidth,
                            'height' => $logoMaxHeight
                        )
                    );
                    $ext = explode('.', $filePath);
                    $logoPath = $this->getUploadPath() . $companyData['logo'] .'.'. $logoMaxWidth .'x'. $logoMaxHeight. "." . $ext[count($ext) - 1];
                    $image->save($logoPath);
                } else {
                    $logoPath = $filePathLogo;
                }
            }*/
        }
        if (empty($logoPath))$logoPath = $this->getRootPath() . "public/logo-pdf.png";

        if (file_exists($logoPath)) {
            $logo = ZendPdf\Image::imageWithPath($logoPath);
            $logoWidth = $logo->getPixelWidth();
            $logoHeight = $logo->getPixelHeight();

            $logoMaxWidth = 140;
            $logoMaxHeight = 80;

            $scale = min($logoMaxWidth / $logoWidth, $logoMaxHeight / $logoHeight);
            $logoNewWidth = (int)($logoWidth * $scale);
            $logoNewHeight = (int)($logoHeight * $scale);

            $this->document->pages[$this->pageIndex]->drawImage($logo, $this->opts['style']['page_padding_left'], $pageHeight - 4 - $logoNewHeight, $this->opts['style']['page_padding_left'] + $logoNewWidth, $pageHeight - 4);
        }

        $headerTitlePaddingRight = 25;
        $headerTitleXOffset = $logoNewWidth + $headerTitlePaddingRight;
        // draw header title >
        $topPosInPage = $pageHeight - 16;
        $text = strtoupper($this->opts['title']);
        $topPosInPage = $this->drawText($text, self::PAGE_HEADER_TITLE_SIZE, '#0F5B8D', $pageHeight - 16, self::TEXT_ALIGN_LEFT, $headerTitleXOffset);

        if (!empty($data['Company name']) && is_string($data['Company name'])) {
            $topPosInPage -= (self::PAGE_HEADER_TITLE_SIZE + (self::BLOCK_TEXT_LINE_SPACING_AT * 2));
            $topPosInPage = $this->drawText($data['Company name'], self::PAGE_HEADER_TITLE_SIZE, '#0F5B8D', $topPosInPage, self::TEXT_ALIGN_LEFT, $headerTitleXOffset);
            unset($data['Company name']);
        }

        if (isset($companyData['address']) && !empty($companyData['address'])) {
            $lines = $this->getTextLines('Address: ' . $companyData['address'], self::PAGE_SUB_HEADER_TITLE_SIZE, 150);
            $k = 0;
            foreach ($lines as $line) {
                $k++;
                if ($k == 4) break;
                $topPosInPage -= (self::PAGE_SUB_HEADER_TITLE_SIZE + (self::BLOCK_TEXT_LINE_SPACING_AT * 2));
                $topPosInPage = $this->drawText($line, self::PAGE_SUB_HEADER_TITLE_SIZE, '#333333', $topPosInPage, self::TEXT_ALIGN_LEFT, $headerTitleXOffset);
            }
        }

        if (isset($companyData['phone']) && !empty($companyData['phone'])) {
            $lines = $this->getTextLines('Phone number: ' . $companyData['phone'], self::PAGE_SUB_HEADER_TITLE_SIZE, 150);
            $k = 0;
            foreach ($lines as $line) {
                $k++;
                if ($k == 4) break;
                $topPosInPage -= (self::PAGE_SUB_HEADER_TITLE_SIZE + (self::BLOCK_TEXT_LINE_SPACING_AT * 2));
                $topPosInPage = $this->drawText($line, self::PAGE_SUB_HEADER_TITLE_SIZE, '#333333', $topPosInPage, self::TEXT_ALIGN_LEFT, $headerTitleXOffset);
            }
        }

        $columnsLeftXOffset = $headerTitleXOffset + 170;
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

        return $topPosInPage - 22;
    }

    protected function drawFooter(\ZendPdf\Page $page)
    {

    }

    protected function getFileByDirAndName($dir, $tosearch)
    {
        if (file_exists($dir) && is_dir($dir)) {

            $validFileExts = array(
                "jpg",
                "jpeg",
                "png"
            );

            $path = $dir . $tosearch;
            $ext = preg_replace('/.*\.([^\.]*)$/is', '$1', $tosearch);
            if (file_exists($path) && is_file($path) && ($ext != $tosearch)) {
                return (realpath($path));
            } else {
                foreach ($validFileExts as $validExt) {
                    $filename = $path . "." . $validExt;
                    if (file_exists($filename) && !is_dir($filename)) {
                        return (realpath($filename));
                    }
                }
            }
        }

        return null;
    }

    protected function getImagePathByName($fileName)
    {
        $filePath = $this->getUploadPath();
        if ($fileName !== null && is_string($fileName)) {
            $fileName = "{$fileName}";
            if (($file = self::getFileByDirAndName($filePath, $fileName)) !== null) {
                $fileSizeInfo = @getimagesize($file);
                if ($fileSizeInfo) { // it`s image
                    return $file;
                }
            }
        }

        return null;
    }


    protected function getTextLines($text, $size, $maxStrWidth = null, $font = null)
    {
        if ($font === null) $font = $this->font;
        $maxStrWidth = (int)$maxStrWidth;
        if (!$maxStrWidth) $maxStrWidth = $this->getPageContentWidth();
        if ($maxStrWidth > $this->getPageContentWidth()) $maxStrWidth = $this->getPageContentWidth();

        $returnLines = array();
        $textLines = explode("\n", ucwords(strip_tags($text)));
        foreach ($textLines as $line) {
            $newLine = "";
            $tmpLine = "";
            $tLine = trim(preg_replace("/\s+/is", " ", $line));
            $tLineWordsArr = wordwrap($tLine, 1, "\n");
            $tLineWordsArr = explode("\n", $tLineWordsArr);
            foreach ($tLineWordsArr as $word) {
                $tmpLine .= " " . $word;
                $tmpLine = trim($tmpLine);
                $strWidth = $this->widthForStringUsingFontSize($tmpLine, $font, $size);
                if ($strWidth > $maxStrWidth) {
                    $returnLines[] = $newLine;
                    $newLine = $tmpLine = $word;
                } else {
                    $newLine = $tmpLine;
                }
            }
            $returnLines[] = $newLine;
        }

        return $returnLines;
    }

    protected function drawText($text, $size, $color, $topYPosition, $align = self::TEXT_ALIGN_LEFT, $xOffset = 0, $font = null, $maxWidth = 0, $forceDetect = false)
    {
        if ($font === null) $font = $this->font;
        $color = ZendPdf\Color\Html::color($color);
        $style = new ZendPdf\Style();
        $style->setFillColor($color);
        $style->setFont($font, $size);

        if (!$maxWidth) $maxWidth = $this->getPageContentWidth();

        if ($forceDetect) {
            $topYPosition = $this->detectNewPage($topYPosition, $size);
        }

        if ($align !== self::TEXT_ALIGN_JUSTIFY) {
            $leftPosInStr = $this->getLeftStartPos($text, $font, $size, $align, $maxWidth);
            $this->document->pages[$this->pageIndex]->setStyle($style)->drawText($text, $leftPosInStr + $xOffset, $topYPosition);
        } else {
            $startPosInLine = $this->getLeftStartPos($text, $font, $size, self::TEXT_ALIGN_LEFT, $maxWidth);
            $pageContentWidth = $this->getPageContentWidth();

            $wordsArr = explode(" ", $text);
            $wordsAmount = count($wordsArr);
            $wordsTotalLength = 0;

            foreach ($wordsArr as $word) {
                $wordsTotalLength += $this->widthForStringUsingFontSize($word, $font, $size);
            }

            $space = ($pageContentWidth - $wordsTotalLength) / (($wordsAmount > 1 ? $wordsAmount : 2) - 1);
            foreach ($wordsArr as $word) {
                $this->document->pages[$this->pageIndex]->setStyle($style)->drawText($word, $startPosInLine, $topYPosition);
                $startPosInLine += ($this->widthForStringUsingFontSize($word, $font, $size) + $space);
            }
        }

        return $topYPosition;
    }


    protected function detectNewPage($startYPosition, $yOffset = 0)
    {
        if ($startYPosition <= $this->opts['style']['page_padding_bottom']) {
            $startYPosition = $this->createNewPage($yOffset);
        }
        return $startYPosition;
    }

    protected function createNewPage($yOffset = 0)
    {
        $this->pageIndex++;
        $this->document->pages[$this->pageIndex] = new ZendPdf\Page($this->pageSize);
        $pageYPosition = $this->getPageHeight();
        $pageYPosition -= $this->opts['style']['page_padding_top'];
        $pageYPosition -= $yOffset;
        return $pageYPosition;
    }

    protected function getLeftStartPos($string = '', $font, $fontSize = 12, $position = self::TEXT_ALIGN_LEFT, $pageContentWidth = 0)
    {
        if (!$pageContentWidth) $pageContentWidth = $this->getPageContentWidth();
        $strWidth = $this->widthForStringUsingFontSize($string, $font, $fontSize);

        switch ($position) {
            case self::TEXT_ALIGN_RIGHT:
                return $this->opts['style']['page_padding_left'] + $pageContentWidth - $strWidth;
                break;
            case self::TEXT_ALIGN_CENTER:
                return $this->opts['style']['page_padding_left'] + (($pageContentWidth - $strWidth) / 2);
                break;
            case self::TEXT_ALIGN_LEFT:
            default:
                return $this->opts['style']['page_padding_left'];
                break;
        }
    }

    protected function getPageWidth()
    {

        if (!empty($this->document->pages[$this->pageIndex])) {
            return $this->document->pages[$this->pageIndex]->getWidth();
        }

        return 0;
    }

    protected function getPageHeight()
    {

        if (!empty($this->document->pages[$this->pageIndex])) {
            return $this->document->pages[$this->pageIndex]->getHeight();
        }

        return 0;
    }

    protected function getPageContentWidth()
    {
        return $this->getPageWidth() - $this->opts['style']['page_padding_left'] - $this->opts['style']['page_padding_right'];
    }

    protected function getPageContentHeight()
    {
        return $this->getPageHeight() - $this->opts['style']['page_padding_top'] - $this->opts['style']['page_padding_bottom'];
    }

    /**
     * Returns the total width in points of the string using the specified font and
     * size.
     *
     * This is not the most efficient way to perform this calculation. I'm
     * concentrating optimization efforts on the upcoming layout manager class.
     * Similar calculations exist inside the layout manager class, but widths are
     * generally calculated only after determining line fragments.
     *
     * @param string $string
     * @param Zend_Pdf_Resource_Font $font
     * @param float|int $fontSize Font size in points
     *
     * @return float
     */
    protected function widthForStringUsingFontSize($string = '', $font, $fontSize = 12)
    {
        $drawingString = iconv('UTF-8', 'UTF-16BE//IGNORE', $string);
        $characters = array();
        for ($i = 0; $i < strlen($drawingString); $i++) {
            $characters[] = (ord($drawingString[$i++]) << 8) | ord($drawingString[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;

        return $stringWidth;
    }

    protected function get_filter_path($fEndPath = null)
    {
        if ($fEndPath === null || !is_string($fEndPath)) {
            $moduleConfig = $this->getController()->getServiceLocator()->get('Config');
            $fEndPath = $moduleConfig['defUsersPath'];
        }

        $root = $this->getRootPath();
        $fEndPath = str_replace("{$root}", '', $fEndPath);
        $fEndPath = str_replace('\\', '/', $fEndPath);

        if (preg_match('/^(\/|.\/).*/isU', $fEndPath, $match)) {
            $fEndPath = preg_replace('/^(\/|.\/).*/isU', "", $fEndPath);
        } else {
            $fEndPath = preg_replace('/^(.*)$/isU', "$1", $fEndPath);
        }

        $returnFolder = '/' . $fEndPath;
        if (!preg_match('/.*(\/)$/isU', $returnFolder, $match)) {
            $returnFolder .= '/';
        }

        return $returnFolder;
    }

    protected function check_dir($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir;
    }

    protected function getServerVar($id)
    {
        return isset($_SERVER[$id]) ? $_SERVER[$id] : '';
    }

    protected function getRootPath()
    {
        $APP_PATH = __DIR__ . '/../../../../../../';
        return $APP_PATH;
    }

    protected function getUploadPath()
    {
        return $this->check_dir($this->getRootPath() . $this->get_filter_path());
    }


    public function getPdfPath()
    {
        return $this->check_dir($this->getUploadPath() . 'pdf/');
    }

    protected function getPdfTmpPath()
    {
        return $this->check_dir($this->getPdfPath() . 'tmp/');
    }

    protected function getName()
    {
        $name = $this->opts['output_name_title'];
        $ext = !empty($this->opts['ext']) ? $this->opts['ext'] : 'pdf';


        return $name . '.' . $ext;
    }

    protected function getFullPath()
    {
        return $this->getPdfPath() . $this->getName();
    }
}
