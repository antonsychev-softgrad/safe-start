<?php namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use ZendPdf;
use SafeStartApi\Model\ImageProcessor;

class InspectionPdfPlugin extends AbstractPlugin
{
    const PAGE_HEADER_TITLE_SIZE = 12;

    const BLOCK_PADDING_TOP = 25;
    const BLOCK_PADDING_BOTTOM = 20;
    const BLOCK_HEADER_SIZE = 19;

    const BLOCK_SUBHEADER_SIZE = 10;
    const BLOCK_SUBHEADER_COLOR_LINE_SIZE = 26;
    const BLOCK_SUBHEADER_COLOR_LINE_PADDING_BOTTOM = 9;

    const BLOCK_TEXT_SIZE = 12;
    const BLOCK_TEXT_LINE_SPACING_BEFORE = 5;
    const BLOCK_TEXT_LINE_SPACING_AFTER = 5;
    const BLOCK_TEXT_LINE_SPACING_AT = 2;


    //colors
    const FOOTER_FONT_COLOR = '#333333';

    const TEXT_ALIGN_LEFT = 'left';
    const TEXT_ALIGN_RIGHT = 'right';
    const TEXT_ALIGN_CENTER = 'center';
    const TEXT_ALIGN_JUSTIFY = 'justify';

    const HEADER_EMPIRIC_HEIGHT = 90;

    private $document;
    private $currentPage;
    private $pageSize = ZendPdf\Page::SIZE_A4_LANDSCAPE;
    private $font;
    private $lastTopPos = 0;
    private $pageIndex = 0;
    private $checkList;
    private $opts = array();
    private $fileName;
    private $filePath;


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
        // add inspection fields
        $this->drawInspection();
        // add additional comments
        $this->drawAlerts();
        // save document
        $this->fileName = $this->getName();
        $this->filePath = $this->getFullPath();
        $this->saveDocument();
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
            'Registration' => $vehicleData['registration'],
            'Type of vehicle' => $vehicleData['type'],
            'Service due' => $vehicleData['serviceDueKm'] . ' km ' . $vehicleData['serviceDueHours'] . ' hours',
            'Current odometer' => $vehicleData['currentOdometerKms'] . ' km ' . $vehicleData['currentOdometerHours'] . ' hours',
            'Next Service Day' => $vehicleData['nextServiceDay'] ? $vehicleData['nextServiceDay'] : '-',
        );
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

    private function drawFooter(\ZendPdf\Page $page)
    {
        $maxHeight = $imageMaxHeight = $this->opts['style']['page_padding_bottom'] / 16 * 10;
        $imageMaxWidth = $imageMaxHeight / 3 * 4;

        $topPosInPage = (($maxHeight) / 2);

        $userData = array();
        $user = $this->checkList->getUser();
        if ($user) $userData = $user->toInfoArray();
        else $userData = (!is_array($this->checkList->getUserData())) ? json_decode((string)$this->checkList->getUserData(), true) : $this->checkList->getUserData();

        $userName = "Name: " . ((isset($userData['firstName']) ? $userData['firstName'] : '') . " " . (isset($userData['lastName']) ? $userData['lastName'] : ''));
        $date = "Date: " . ($this->checkList->getCreationDate()->format($this->getController()->moduleConfig['params']['date_format'] . ' ' . $this->getController()->moduleConfig['params']['time_format']));
        $signature = "Signature: ";

        $color = ZendPdf\Color\Html::color(self::FOOTER_FONT_COLOR);
        $style = new ZendPdf\Style();
        $style->setFillColor($color);
        $style->setFont($this->font, self::BLOCK_TEXT_SIZE);
        $page->setStyle($style);

        $leftPosInStr = $this->getLeftStartPos($userName, $this->font, self::BLOCK_TEXT_SIZE, self::TEXT_ALIGN_LEFT);
        $page->drawText($userName, $leftPosInStr, $topPosInPage);

        $strWidth = $this->widthForStringUsingFontSize($signature, $this->font, self::BLOCK_TEXT_SIZE);
        $leftPosInStr = $this->getLeftStartPos($signature, $this->font, self::BLOCK_TEXT_SIZE, self::TEXT_ALIGN_CENTER);
        $page->drawText($signature, $leftPosInStr - ($imageMaxWidth / 2), $topPosInPage);

        if (($signaturePath = $this->getImagePathByName(isset($userData['signature']) ? $userData['signature'] : '')) !== null) {
            $image = ZendPdf\Image::imageWithPath($signaturePath);
            $imageWidth = $image->getPixelWidth();
            $imageHeight = $image->getPixelHeight();

            $scale = min($imageMaxWidth / $imageWidth, $imageMaxHeight / $imageHeight);
            $imageNewWidth = (int)($imageWidth * $scale);
            $imageNewHeight = (int)($imageHeight * $scale);

            $page->drawImage($image,
                $leftPosInStr - ($imageMaxWidth / 2) + $strWidth,
                $topPosInPage + (($imageMaxHeight + self::BLOCK_TEXT_SIZE) / 2) - $imageNewHeight,
                $leftPosInStr - ($imageMaxWidth / 2) + $strWidth + $imageNewWidth,
                $topPosInPage + (($imageMaxHeight + self::BLOCK_TEXT_SIZE) / 2));
        }

        $leftPosInStr = $this->getLeftStartPos($date, $this->font, self::BLOCK_TEXT_SIZE, self::TEXT_ALIGN_RIGHT);
        $page->drawText($date, $leftPosInStr, $topPosInPage);

        return true;
    }

    private function drawInspection()
    {
        $vehicleDetails = array();
        $alertsDetails = array();
        $fieldsStructure = json_decode($this->checkList->getFieldsStructure());
        $fieldsData = json_decode($this->checkList->getFieldsData(), true);
        $fieldsDataValues = array();
        foreach ($fieldsData as $fieldData) $fieldsDataValues[$fieldData['id']] = $fieldData['value'];

        $columns = $this->opts['style']['content_columns'];
        $columnsPadding = $this->opts['style']['content_column_padding'];
        $contentWidth = $this->getPageContentWidth() * $this->opts['style']['content_width'];
        $columnWidth = round(($contentWidth - ($columnsPadding * ($columns - 1))) / $columns);
        $currentColumn = 1;
        $this->lastTopPos -= 10;

        foreach ($fieldsStructure as $groupBlock) {
            if ($this->_isEmptyGroup($groupBlock, $fieldsDataValues)) continue;
            $text = (isset($groupBlock->fieldDescription) && !empty($groupBlock->fieldDescription)) ? $groupBlock->fieldDescription : $groupBlock->groupName;
            $lines = $this->getTextLines($text, $this->opts['style']['category_field_size'], $columnWidth);
            foreach ($lines as $line) {
                if ($this->lastTopPos <= $this->opts['style']['page_padding_bottom']) {
                    $currentColumn++;
                    if ($currentColumn > $columns) {
                        $this->pageIndex++;
                        $this->document->pages[$this->pageIndex] = new ZendPdf\Page($this->pageSize);
                        $currentColumn = 1;
                    }
                    $this->lastTopPos = ($this->pageIndex) ? $this->getPageHeight() - $this->opts['style']['page_padding_top'] : $this->getPageHeight() - self::HEADER_EMPIRIC_HEIGHT;
                }
                $this->drawText($line, $this->opts['style']['category_field_size'], $this->opts['style']['category_field_color'], $this->lastTopPos, self::TEXT_ALIGN_CENTER, ($this->opts['style']['page_padding_left'] + ($currentColumn - 1) * $columnWidth), $this->font, $columnWidth);

                $this->lastTopPos -= ($this->opts['style']['category_field_size'] + ($this->opts['style']['category_field_line_spacing'] * 2));

                if (isset($groupBlock->fields)) $currentColumn = $this->drawInspectionFields($groupBlock->fields, $fieldsDataValues, $currentColumn);

                $this->lastTopPos -= 10;
            }

        }
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
            $text = (isset($field->fieldDescription) && !empty($field->fieldDescription)) ? $field->fieldDescription : (string)$field->fieldName;
            $lines = array_filter($this->getTextLines($text, $this->opts['style']['field_size'], ($field->type == 'group') ? $columnWidth : $columnFieldTitleWidth));
            $startYPos = $this->lastTopPos;
            foreach ($lines as $line) {
                if ($this->lastTopPos <= $this->opts['style']['page_padding_bottom']) {
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
                    ($field->type == 'group') ? $this->opts['style']['field_group_color']: $this->opts['style']['field_color'] ,
                    $this->lastTopPos,
                    ($field->type == 'group') ? self::TEXT_ALIGN_CENTER : self::TEXT_ALIGN_LEFT ,
                    ($this->opts['style']['page_padding_left'] + ($currentColumn - 1) * $columnWidth) + $columnsPadding / 2,
                    $this->font,
                    ($field->type == 'group') ? $columnWidth : $columnFieldTitleWidth
                );
                $this->lastTopPos -= ($this->opts['style']['field_size'] + ($this->opts['style']['field_line_spacing'] * 2));
            }
            if ($field->type != 'group') {
                if ($field->type == 'datePicker' && isset($fieldsDataValues[$field->id]) && !empty($fieldsDataValues[$field->id])) {
                    $value = gmdate($this->getController()->moduleConfig['params']['date_format'], (int)$fieldsDataValues[$field->id]);
                } else {
                    $value = (isset($fieldsDataValues[$field->id]) && !empty($fieldsDataValues[$field->id])) ? $fieldsDataValues[$field->id] : '-';
                }
                if (!$field->additional && (strtolower($field->triggerValue) == strtolower($value))) {
                    $value =  $this->opts['style']['field_alert_text'];
                    $color = $this->opts['style']['field_alert_color'];
                } else {
                    $color = $this->opts['style']['field_ok_color'];
                }
                $value = strtoupper($value);
                $this->drawText(
                    $value,
                    $this->opts['style']['field_size'],
                    $color,
                    ($startYPos - (count($lines) - 1) * (($this->opts['style']['field_size'] + ($this->opts['style']['field_line_spacing'] * 2)) / 2)),
                    self::TEXT_ALIGN_RIGHT,
                    ($this->opts['style']['page_padding_left'] + ($currentColumn - 1) * $columnWidth) + ($columnFieldTitleWidth),
                    $this->font,
                    $columnFieldValueWidth
                );
            }
            if (!empty($field->items)) $currentColumn = $this->drawInspectionFields($field->items, $fieldsDataValues, $currentColumn);
        }
        return $currentColumn;
    }


    public function getFilePathByName($name = '')
    {
        return $this->getPdfPath() . $name;
    }

    private function saveDocument()
    {
        foreach ($this->document->pages as $page) {
            $this->drawFooter($page);
        }
        $this->document->save($this->filePath);
        chmod($this->filePath, 0777);
        $this->checkList->setPdfLink($this->fileName);
        $this->getController()->em->flush();
    }

    private function getFileByDirAndName($dir, $tosearch)
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

    private function getImagePathByName($fileName)
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

    private function _isEmptyGroup($group, $fieldsDataValues)
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
                if (!$this->_isEmptyGroup($field, $fieldsDataValues)) return false;
            }
            if (!empty($fieldsDataValues[$field->id])) {
                return false;
            }
        }
        return true;
    }

    private function getTextLines($text, $size, $maxStrWidth = null, $font = null)
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

    private function drawText($text, $size, $color, $topYPosition, $align = self::TEXT_ALIGN_LEFT, $xOffset = 0, $font = null, $maxWidth = 0, $forceDetect = false)
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


    private function detectNewPage($startYPosition, $yOffset = 0)
    {
        if ($startYPosition <= $this->opts['style']['page_padding_bottom']) {
            $startYPosition = $this->createNewPage($yOffset);
        }
        return $startYPosition;
    }

    private function createNewPage($yOffset = 0)
    {
        $this->pageIndex++;
        $this->document->pages[$this->pageIndex] = new ZendPdf\Page($this->pageSize);
        $pageYPosition = $this->getPageHeight();
        $pageYPosition -= $this->opts['style']['page_padding_top'];
        $pageYPosition -= $yOffset;
        return $pageYPosition;
    }

    private function getLeftStartPos($string = '', $font, $fontSize = 12, $position = self::TEXT_ALIGN_LEFT, $pageContentWidth = 0)
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

    private function getPageWidth()
    {

        if (!empty($this->document->pages[$this->pageIndex])) {
            return $this->document->pages[$this->pageIndex]->getWidth();
        }

        return 0;
    }

    private function getPageHeight()
    {

        if (!empty($this->document->pages[$this->pageIndex])) {
            return $this->document->pages[$this->pageIndex]->getHeight();
        }

        return 0;
    }

    private function getPageContentWidth()
    {
        return $this->getPageWidth() - $this->opts['style']['page_padding_left'] - $this->opts['style']['page_padding_right'];
    }

    private function getPageContentHeight()
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
    private function widthForStringUsingFontSize($string = '', $font, $fontSize = 12)
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

    private function get_filter_path($fEndPath = null)
    {
        if ($fEndPath === null || !is_string($fEndPath)) {
            $fEndPath = $this->uploadPath;
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

    private function check_dir($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir;
    }

    private function getServerVar($id)
    {
        return isset($_SERVER[$id]) ? $_SERVER[$id] : '';
    }

    private function getRootPath()
    {
        $root = $this->getServerVar('DOCUMENT_ROOT');
        // check root
        if (!file_exists($root . "/init_autoloader.php")) {
            $root = dirname($root);
        }

        return $root;
    }

    private function getUploadPath()
    {
        return $this->check_dir($this->getRootPath() . $this->get_filter_path());
    }


    public function getPdfPath()
    {
        return $this->check_dir($this->getUploadPath() . 'pdf/');
    }

    private function getPdfTmpPath()
    {
        return $this->check_dir($this->getPdfPath() . 'tmp/');
    }

    private function getName()
    {
        $name = $this->opts['output_name_title'];
        $ext = !empty($this->opts['ext']) ? $this->opts['ext'] : '.pdf';

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

        return $template . $ext;
    }

    private function getFullPath()
    {
        return $this->getPdfPath() . $this->getName();
    }
}
