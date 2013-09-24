<?php namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use ZendPdf;
use SafeStartApi\Model\ImageProcessor;

class PdfPlugin extends AbstractPlugin
{

    const PAGE_PADDING_LEFT   = 58;
    const PAGE_PADDING_TOP    = 24;
    const PAGE_PADDING_RIGHT  = 58;
    const PAGE_PADDING_BOTTOM = 70;

    const PAGE_HEADER_TITLE_SIZE = 22;

    const BLOCK_PADDING_TOP                         = 25;
    const BLOCK_PADDING_BOTTOM                      = 20;
    const BLOCK_HEADER_SIZE                         = 19;
    const BLOCK_SUBHEADER_SIZE                      = 16;
    const BLOCK_SUBHEADER_COLOR_LINE_SIZE           = 36;
    const BLOCK_SUBHEADER_COLOR_LINE_PADDING_BOTTOM = 13;
    const BLOCK_TEXT_SIZE                           = 12;
    const BLOCK_TEXT_LINE_SPACING_BEFORE            = 5;
    const BLOCK_TEXT_LINE_SPACING_AFTER             = 5;
    const BLOCK_TEXT_LINE_SPACING_AT                = 2;

    const TEXT_ALIGN_LEFT    = 'left';
    const TEXT_ALIGN_RIGHT   = 'right';
    const TEXT_ALIGN_CENTER  = 'center';
    const TEXT_ALIGN_JUSTIFY = 'justify';

    protected $document;
    protected $currentPage;
    protected $font;
    protected $checkList;
    protected $uploadPath;
    protected $docHeaderTitle = 'checklist review';
    protected $opts = array();
    protected $dateGeneration;
    protected $fieldsData = array();
    protected $file_name;
    protected $full_name;

    protected $echoPdf = true;
    protected $emailMode = false;

    public function __invoke($checkListId = null, $emailMode = false)
    {
        $moduleConfig     = $this->getController()->getServiceLocator()->get('Config');
        $this->opts       = $moduleConfig['pdf'];
        $this->uploadPath = $moduleConfig['defUsersPath'];

        if ($checkListId === null) {
            return $this;
        }

        $this->emailMode = $emailMode;
        return $this->create($checkListId);
    }

    public function create($checkListId = null)
    {
        $this->document       = new ZendPdf\PdfDocument();
        $this->currentPage    = new ZendPdf\Page(ZendPdf\Page::SIZE_A4);

        $fontPath = dirname(__FILE__) . "/../../../../public/fonts/HelveticaNeueLTStd-Cn.ttf";
        $this->font = file_exists($fontPath) ? ZendPdf\Font::fontWithPath($fontPath) : ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_HELVETICA);

        /**/
        $this->checkList = $this->getController()->em->find('SafeStartApi\Entity\CheckList', $checkListId);
        if ($this->checkList === null) {
            throw new \Exception('CheckList not found.');
        }
        $this->dateGeneration = $this->checkList->getCreationDate();

        $vehicleDetails = array();
        $alertsDetails  = array();
        $vehicleData = array();

        if ($this->getController()->authService->hasIdentity()) {
            $vehicle = $this->checkList->getVehicle();
            $company = $vehicle->getCompany();
            $vehicleData = $vehicle->toInfoArray();
            $companyData = $company ? $company->toArray() : array();
            $vehicleData = array(
                'Company name' => isset($companyData['title']) ? $companyData['title'] : '',
                'Vehivcle title' => $vehicleData['title'],
                'Project number' => $vehicleData['projectNumber'],
                'Project name' => $vehicleData['projectName'],
                'Plant ID / Registration' => $vehicleData['plantId'] .' / '. $vehicleData['registration'],
                'Type of vehicle' => $vehicleData['type'],
                'Service due' => $vehicleData['serviceDueKm'] .' km '. $vehicleData['serviceDueHours'] . ' hours',
                'Current odometr' => $vehicleData['currentOdometerKms'] .' km '. $vehicleData['currentOdometerHours'] . ' hours',
            );
        }
        $fieldsStruct   = json_decode($this->checkList->getFieldsStructure());
        $fieldsData   = json_decode($this->checkList->getFieldsData(), true);
        foreach($fieldsData as $fieldData) {
            $this->fieldsData[$fieldData['id']] = $fieldData['value'];
        }
        foreach ($fieldsStruct as $groupBlock) {

            $group         = new \stdClass();
            $group->name   = $groupBlock->groupName;
            $group->alerts = array();

            // get field id`s
            $filds    = $groupBlock->fields;
            $fieldIds = array();
            foreach ($filds as $fild) {
                $fieldIds[] = $fild->id;
            }

            // get alerts
            if (!empty($fieldIds)) {

                if($this->emailMode) {
                    $query = $this->getController()->em->createQuery('SELECT a FROM SafeStartApi\Entity\DefaultAlert a WHERE a.check_list = :cl AND a.default_field IN (' . implode(',', $fieldIds) . ')');
                } else {
                    $query = $this->getController()->em->createQuery('SELECT a FROM SafeStartApi\Entity\Alert a WHERE a.check_list = :cl AND a.field IN (' . implode(',', $fieldIds) . ')');
                }
                $query->setParameters(array('cl' => $this->checkList));
                $group->alerts = $query->getResult();
                if (is_array($group->alerts) && !empty($group->alerts)) {
                    foreach ($group->alerts as $alert) {

                        $alertInfo          = new \stdClass();
                        $alertInfo->title   = $alert->getField()->getAlertTitle();
                        $alertInfo->description   = $alert->getField()->getAlertDescription();
                        $alertInfo->comment = $alert->getDescription();

                        $images     = array();
                        $imagesInfo = $alert->getImages();

                        if ($imagesInfo !== null) {
                            // fix type
                            if (is_string($imagesInfo)) {
                                $imagesInfo = json_decode($imagesInfo);
                            } elseif (is_array($imagesInfo)) {
                            }

                            if (is_array($imagesInfo) && !empty($imagesInfo)) {
                                foreach ($imagesInfo as $imageName) {
                                    if (($imagePath = $this->getImagePathByName($imageName)) !== null) {
                                        $images[] = $imagePath;
                                    }
                                }
                            }
                        }
                        $alertInfo->images                          = $images;
                        $alertsDetails[$alert->getField()->getId()] = $alertInfo;
                    }
                }
            }

            // set status
            $group->status    = empty($group->alerts) ? 'ok' : 'alert';
            $group->fields = $filds;
            $vehicleDetails[] = $group;
        }
        /**/

        // header >
        $topPosInPage = $this->drawHeader();
        // draw company details
        $topPosInPage = $this->drawTextBlock('company', 'Company details', $vehicleData, $topPosInPage);

        // draw vehicle details block
        $topPosInPage = $this->drawTextBlock('vehicle', 'Checklist', $vehicleDetails, $topPosInPage);

        // draw alerts block >
        $topPosInPage = $this->drawTextBlock('alerts', 'alerts', $alertsDetails, $topPosInPage);

        $this->drawFooter();
        $this->document->pages[] = $this->currentPage;
        $this->file_name = $this->get_name();
        $this->full_name = $this->get_full_name();

        $this->savePdf();
        return $this->printPdf($this->file_name, $this->emailMode);
    }

    public function printPdf($name, $emailMode = false)
    {
        $path = $this->get_pdf_path() . $name;
        if (!$emailMode) {
            /**/
            header("Content-Disposition: inline; filename={$name}");
            header("Content-type: application/x-pdf");
            echo file_get_contents($path);
            /**/
        }

        return $path;
    }

    protected function savePdf()
    {
        $this->document->save($this->full_name);
        chmod($this->full_name, 0777);
        $this->checkList->setPdfLink($this->file_name);
        $this->getController()->em->flush();
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
            $ext  = preg_replace('/.*\.([^\.]*)$/is', '$1', $tosearch);
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
            /* banned > * /
            $flags = \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS;
            $iterator = new \RecursiveDirectoryIterator($dir, $flags);
            $iterator = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST, \RecursiveIteratorIterator::CATCH_GET_CHILD);
            foreach ($iterator as $file) {
                $tosearch = urldecode($tosearch);
                $fileInfo = pathinfo($file);
                if(($fileInfo['filename'] == $tosearch) || ($fileInfo['basename'] == $tosearch)) {
                    return $file;
                }
            }
            /* > end. */
        }

        return null;
    }

    protected function getImagePathByName($fileName)
    {
        $filePath = $this->get_upload_path();
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

    protected function drawHeader()
    {

        $pageHeight = $this->getPageHeight();
        $pageWidth  = $this->getPageWidth();

        // draw logo image >
        $root = $this->get_root_path();

        $logoMaxWidth  = 199;
        $logoMaxHeight = 53;
        $logoPath      = "{$root}/public/logo-pdf.png";

        $logo       = ZendPdf\Image::imageWithPath($logoPath);
        $logoWidth  = $logo->getPixelWidth();
        $logoHeight = $logo->getPixelHeight();

        $scale         = min($logoMaxWidth / $logoWidth, $logoMaxHeight / $logoHeight);
        $logoNewWidth  = (int) ($logoWidth * $scale);
        $logoNewHeight = (int) ($logoHeight * $scale);

        $this->currentPage->drawImage($logo, 56, $pageHeight - 24 - $logoNewHeight, 56 + $logoNewWidth, $pageHeight - 24);
        // > end draw logo image.

        // draw header title >
        $text = strtoupper($this->docHeaderTitle);
        $this->drawText($text, self::PAGE_HEADER_TITLE_SIZE, '#0F5B8D', $pageHeight - 60, self::TEXT_ALIGN_RIGHT);
        // > end draw header title.

        // draw header line >
        $style     = new ZendPdf\Style();
        $lineColor = ZendPdf\Color\Html::color('#FFEB40');
        $style->setLineColor($lineColor);
        $style->setFillColor($lineColor);
        $style->setLineWidth(1);
        $this->currentPage->setStyle($style)->drawLine(0, $pageHeight - 102, $pageWidth, $pageHeight - 102);

        $style     = new ZendPdf\Style();
        $lineColor = ZendPdf\Color\Html::color('#FEE401');
        $style->setLineColor($lineColor);
        $style->setFillColor($lineColor);
        $style->setLineWidth(1);
        $this->currentPage->setStyle($style)->drawLine(0, $pageHeight - 103, $pageWidth, $pageHeight - 103);

        $style     = new ZendPdf\Style();
        $lineColor = ZendPdf\Color\Html::color('#FEFAD3');
        $style->setLineColor($lineColor);
        $style->setFillColor($lineColor);
        $style->setLineWidth(1);
        $this->currentPage->setStyle($style)->drawLine(0, $pageHeight - 104, $pageWidth, $pageHeight - 104);

        // > end draw header line.
        $topPosInPage = ($pageHeight -= 105);

        return $topPosInPage;
    }

    protected function drawFooter()
    {
        if ($drawFooter = true) {
            $font      = $this->font;
            $fontSize  = self::BLOCK_TEXT_SIZE;
            $fontColor = "#333333";

            $logoMaxHeight = self::PAGE_PADDING_BOTTOM / 16 * 10;
            $logoMaxWidth  = $logoMaxHeight / 3 * 4;

            $topPosInPage = (($logoMaxHeight) / 2);

            $user    = $this->checkList->getUser();
            if (!$user) return;
            $vehicle = $this->checkList->getVehicle();

            $name      = $user ? "Name: " . $user->getFirstName() . " " . $user->getLastName() : '';
            $signature = "Signature: ";
            $date      = "Date: " . $this->dateGeneration->format($this->getController()->moduleConfig['params']['date_format'] .' '. $this->getController()->moduleConfig['params']['time_format']);

            $color = ZendPdf\Color\Html::color($fontColor);
            $style = new ZendPdf\Style();
            $style->setFillColor($color);
            $style->setFont($font, $fontSize);
            $this->currentPage->setStyle($style);

            $leftPosInStr = $this->getLeftStartPos($name, $font, $fontSize, self::TEXT_ALIGN_LEFT);
            $this->currentPage->drawText($name, $leftPosInStr, $topPosInPage);

            $strWidth     = $this->widthForStringUsingFontSize($signature, $font, $fontSize);
            $leftPosInStr = $this->getLeftStartPos($signature, $font, $fontSize, self::TEXT_ALIGN_CENTER);
            if (($logoPath = $this->getImagePathByName($user->getSignature())) !== null) {
                $this->currentPage->drawText($signature, $leftPosInStr - ($logoMaxWidth / 2), $topPosInPage);
                $logo       = ZendPdf\Image::imageWithPath($logoPath);
                $logoWidth  = $logo->getPixelWidth();
                $logoHeight = $logo->getPixelHeight();

                $scale         = min($logoMaxWidth / $logoWidth, $logoMaxHeight / $logoHeight);
                $logoNewWidth  = (int) ($logoWidth * $scale);
                $logoNewHeight = (int) ($logoHeight * $scale);

                $this->currentPage->drawImage($logo, $leftPosInStr - ($logoMaxWidth / 2) + $strWidth, $topPosInPage + (($logoMaxHeight + $fontSize) / 2) - $logoNewHeight, $leftPosInStr - ($logoMaxWidth / 2) + $strWidth + $logoNewWidth, $topPosInPage + (($logoMaxHeight + $fontSize) / 2));
            }

            $leftPosInStr = $this->getLeftStartPos($date, $font, $fontSize, self::TEXT_ALIGN_RIGHT);
            $this->currentPage->drawText($date, $leftPosInStr, $topPosInPage);
        }
    }

    protected function drawTextBlock($type, $headerTitle, $params, $topPosInPage)
    {

        if (is_array($params) && !empty($params)) {
            $topPosInPage -= self::BLOCK_PADDING_TOP;

            switch (strtolower($type)) {
                case 'company':
                    $topPosInPage = $this->_drawVehicleInfo($params, $topPosInPage);
                    break;
                case 'vehicle':
                    $topPosInPage = $this->drawVehicleBlock($headerTitle, $params, $topPosInPage);
                    break;
                case 'alerts':
                    $topPosInPage = $this->drawAlertsBlock($headerTitle, $params, $topPosInPage);
                    break;
            }

            $topPosInPage -= self::BLOCK_PADDING_BOTTOM;
        }

        return $topPosInPage;
    }

    protected function drawVehicleBlock($headerTitle, $params, $topPosInPage)
    {
        $text         = ucfirst($headerTitle);

        $topPosInPage += self::BLOCK_PADDING_BOTTOM;
        $topPosInPage = $this->drawText($text, self::BLOCK_HEADER_SIZE, '#0F5B8D', $topPosInPage, self::TEXT_ALIGN_CENTER);

        $topPosInPage -= self::BLOCK_SUBHEADER_COLOR_LINE_PADDING_BOTTOM + self::BLOCK_PADDING_BOTTOM;
        foreach ($params as $group) {

            if (is_array($group) && !empty($group)) {
                $title  = $group['name'];
                $status = $group['status'];
            } elseif ($group instanceof \stdClass) {
                $title  = $group->name;
                $status = $group->status;
            } else {
                continue;
            }
            $anyValue = false;
            foreach($group->fields as $field) {
                if(!empty($this->fieldsData[$field->id]) || $field->type == 'group') {
                    $anyValue = true;
                    break;
                }
            }
            if(!$anyValue) continue;

            $title          = strip_tags($title);
            $title          = ucwords($title);
            $headlineArray  = $this->getTextLines($title, self::BLOCK_SUBHEADER_SIZE);
            foreach ($headlineArray as $line) {

                $text         = trim($line);
                $topPosInPage = $this->drawText($text, self::BLOCK_SUBHEADER_SIZE, '#333333', $topPosInPage, self::TEXT_ALIGN_CENTER);
                $topPosInPage -= self::BLOCK_SUBHEADER_COLOR_LINE_SIZE;
            }

            $topPosInPage = $this->_drawFields($group->fields, $topPosInPage);
        }

        return $topPosInPage;
    }

    protected function _drawFields($fields = array(), $topPosInPage)
    {
        if(empty($fields)) return $topPosInPage;

        $pageWidth = $this->getPageWidth();
        $contentWidth = $this->getPageContentWidth() - 50;

        $lineCounter = 0;
        foreach ($fields as $field) {

            if($field->type == 'group') {
                $lineCounter = 0;
            }
            $drawLine = (bool) (++$lineCounter % 2);
            $fLinePos = $topPosInPage;

            $title  = $field->fieldName;
            $value = !empty($this->fieldsData[$field->id]) ? $this->fieldsData[$field->id] : '-';
            if($field->type == 'datePicker' && !empty($this->fieldsData[$field->id])) {
                if(!is_int($value)) {
                    $value = strtotime($value);
                }
                $value = gmdate($this->getController()->moduleConfig['params']['date_format'] .' '. $this->getController()->moduleConfig['params']['time_format'], $value);
            }

            $title          = strip_tags($title);
            $title          = ucwords($title);
            $headlineArray  = $this->getTextLines($title, self::BLOCK_SUBHEADER_SIZE, null, $contentWidth);
            $subLineCounter = 0;
            foreach ($headlineArray as $line) {
                if ($drawLine) {
                    // first color
                } else {
                    // second color
                    $lineStartYPos = $topPosInPage - self::BLOCK_SUBHEADER_COLOR_LINE_PADDING_BOTTOM;
                    $topPosInPage  = $lineStartYPos = $this->detectNewPage($lineStartYPos, self::BLOCK_SUBHEADER_COLOR_LINE_SIZE);
                    $topPosInPage += self::BLOCK_SUBHEADER_COLOR_LINE_PADDING_BOTTOM;

                    $lineColor = ZendPdf\Color\Html::color('#EBEBEB');
                    $lineStyle = new ZendPdf\Style();
                    $lineStyle->setLineColor($lineColor);
                    $lineStyle->setFillColor($lineColor);
                    $this->currentPage->setStyle($lineStyle)->drawRectangle(0, $lineStartYPos, $pageWidth, $lineStartYPos + self::BLOCK_SUBHEADER_COLOR_LINE_SIZE);
                }

                $text         = trim($line);
                if($field->type == 'group') {
                    $topPosInPage = $this->drawText($text, self::BLOCK_SUBHEADER_SIZE, '#333333', $topPosInPage, self::TEXT_ALIGN_CENTER);
                } else {
                    $topPosInPage = $this->drawText($text, self::BLOCK_SUBHEADER_SIZE, '#333333', $topPosInPage);
                }

                if (!$subLineCounter++) {
                    $fLinePos = $topPosInPage;
                }

                $topPosInPage -= self::BLOCK_SUBHEADER_COLOR_LINE_SIZE;
            }

            // draw status >
            if(!$field->additional && isset($field->alerts) && ($field->triggerValue == strtolower($value))) {
                $value = 'alert';
                $color = "#ff0000";
            } else {
                $color = "#0f5b8d";
            }
            $value = strtoupper($value);

            if($field->type == 'group') {
                $topPosInPage = $this->_drawFields($field->items, $topPosInPage);
            } else {
                $this->drawText($value, self::BLOCK_SUBHEADER_SIZE, $color, $fLinePos, self::TEXT_ALIGN_RIGHT);
            }

        }

        $topPosInPage -= (self::BLOCK_SUBHEADER_COLOR_LINE_PADDING_BOTTOM);
        return $topPosInPage;
    }

    protected function _drawVehicleInfo($data = array(), $topPosInPage)
    {
        if(empty($data)) return $topPosInPage;
        $topPosInPage -= (self::BLOCK_SUBHEADER_COLOR_LINE_SIZE + self::BLOCK_SUBHEADER_COLOR_LINE_PADDING_BOTTOM);

        $pageWidth = $this->getPageWidth();
        $contentWidth = $this->getPageContentWidth() - 50;

        $lineCounter = 0;
        foreach ($data as $key => $value) {

            $drawLine = (bool) (++$lineCounter % 2);
            $fLinePos = $topPosInPage;

            $title  = $key;
            $value = (!empty($value) && !is_null($value)) ? (string)$value : '-';

            $title          = strip_tags($title);
            $title          = ucwords($title);
            $headlineArray  = $this->getTextLines($title, self::BLOCK_SUBHEADER_SIZE, null, $contentWidth);
            $subLineCounter = 0;
            foreach ($headlineArray as $line) {
                if ($drawLine) {
                    // first color
                } else {
                    // second color
                    $lineStartYPos = $topPosInPage - self::BLOCK_SUBHEADER_COLOR_LINE_PADDING_BOTTOM;
                    $topPosInPage  = $lineStartYPos = $this->detectNewPage($lineStartYPos, self::BLOCK_SUBHEADER_COLOR_LINE_SIZE);
                    $topPosInPage += self::BLOCK_SUBHEADER_COLOR_LINE_PADDING_BOTTOM;

                    $lineColor = ZendPdf\Color\Html::color('#EBEBEB');
                    $lineStyle = new ZendPdf\Style();
                    $lineStyle->setLineColor($lineColor);
                    $lineStyle->setFillColor($lineColor);
                    $this->currentPage->setStyle($lineStyle)->drawRectangle(0, $lineStartYPos, $pageWidth, $lineStartYPos + self::BLOCK_SUBHEADER_COLOR_LINE_SIZE);
                }

                $text         = trim($line);
                $topPosInPage = $this->drawText($text, self::BLOCK_SUBHEADER_SIZE, '#333333', $topPosInPage);

                if (!$subLineCounter++) {
                    $fLinePos = $topPosInPage;
                }

                $topPosInPage -= self::BLOCK_SUBHEADER_COLOR_LINE_SIZE;
            }

            // draw status >
            $color = "#0f5b8d";
            $value = strtoupper($value);

            $this->drawText($value, self::BLOCK_SUBHEADER_SIZE, $color, $fLinePos, self::TEXT_ALIGN_RIGHT);

        }

        return $topPosInPage;
    }

    protected function drawAlertsBlock($headerTitle, $params, $topPosInPage)
    {

        $topPosInPage -= self::BLOCK_HEADER_SIZE;
        $text         = ucfirst($headerTitle);
        $topPosInPage = $this->drawText($text, self::BLOCK_HEADER_SIZE, '#ff0000', $topPosInPage);

        foreach ($params as $alertInfo) {

            if (is_array($alertInfo) && !empty($alertInfo)) {
                $title   = $alertInfo['title'];
                $description = $alertInfo['description'];
                $comment = $alertInfo['comment'];
                $images  = $alertInfo['images'];
            } elseif ($alertInfo instanceof \stdClass) {
                $title   = $alertInfo->title;
                $description = $alertInfo->description;
                $comment = $alertInfo->comment;
                $images  = $alertInfo->images;
            } else {
                continue;
            }

            $topPosInPage -= (self::BLOCK_SUBHEADER_COLOR_LINE_SIZE + self::BLOCK_SUBHEADER_COLOR_LINE_PADDING_BOTTOM);

            if(!is_null($description)) {
                $title = $description;
            }
            $title = strip_tags($title);
            $headlineArray = $this->getTextLines($title, self::BLOCK_SUBHEADER_SIZE);
            $lineCounter   = count($headlineArray);
            foreach ($headlineArray as $line) {
                $text         = trim($line);
                $topPosInPage = $this->drawText($text, self::BLOCK_SUBHEADER_SIZE, '#ff0000', $topPosInPage);
                if ((--$lineCounter) > 0) {
                    $topPosInPage -= (self::BLOCK_SUBHEADER_SIZE + self::BLOCK_TEXT_LINE_SPACING_AT);
                } else {
                    if (!empty($comment)) {
                        $topPosInPage -= self::BLOCK_SUBHEADER_COLOR_LINE_SIZE;
                    }
                }
            }

            if (!empty($comment)) {
                    $comment       = strip_tags($comment);
                    $headlineArray = $this->getTextLines($comment, self::BLOCK_TEXT_SIZE);
                    $lineCounter   = count($headlineArray);
                    foreach ($headlineArray as $line) {
                        $text         = trim($line);
                        $topPosInPage = $this->drawText($text, self::BLOCK_TEXT_SIZE, '#333333', $topPosInPage);
                        $topPosInPage -= self::BLOCK_TEXT_SIZE;
                        if ((--$lineCounter) > 0) {
                            $topPosInPage -= self::BLOCK_TEXT_LINE_SPACING_AT;
                        }
                    }
            }

            if (!empty($images) && is_array($images)) {
                $topPosInPage = $this->drawImagesInColumns($topPosInPage, $images);
            }
        }

        return $topPosInPage;
    }

    protected function getTextLines($text, $size, $font = null, $maxStrWidth = null)
    {

        if ($font === null) {
            $font = $this->font;
        }

        $pageContentWidth = $this->getPageContentWidth();
        if ($maxStrWidth === null || !is_int($maxStrWidth)) {
            $maxStrWidth = $pageContentWidth;
        } elseif (is_int($maxStrWidth)) {
            if ($maxStrWidth > $pageContentWidth) {
                $maxStrWidth = $pageContentWidth;
            }
        }

        $returnLines = array();
        $textLines   = explode("\n", $text);
        foreach ($textLines as $line) {
            $newLine       = "";
            $tmpLine       = "";
            $tLine         = trim(preg_replace("/\s+/is", " ", $line));
            $tLineWordsArr = wordwrap($tLine, 1, "\n");
            $tLineWordsArr = explode("\n", $tLineWordsArr);
            foreach ($tLineWordsArr as $word) {
                $tmpLine .= " " . $word;
                $tmpLine  = trim($tmpLine);
                $strWidth = $this->widthForStringUsingFontSize($tmpLine, $font, $size);
                if ($strWidth > $maxStrWidth) {
                    $returnLines[] = $newLine;
                    $newLine       = $tmpLine = $word;
                } else {
                    $newLine = $tmpLine;
                }
            }
            $returnLines[] = $newLine;
        }

        return $returnLines;
    }

    protected function drawText($text, $size, $color, $topYPosition, $align = self::TEXT_ALIGN_LEFT, $font = null)
    {

        if ($font === null) {
            $font = $this->font;
        }

        $color = ZendPdf\Color\Html::color($color);
        $style = new ZendPdf\Style();
        $style->setFillColor($color);
        $style->setFont($font, $size);

        $topYPosition = $this->detectNewPage($topYPosition, $size);
        if ($align !== self::TEXT_ALIGN_JUSTIFY) {
            $leftPosInStr = $this->getLeftStartPos($text, $font, $size, $align);
            $this->currentPage->setStyle($style)->drawText($text, $leftPosInStr, $topYPosition);
        } else {

            $startPosInLine   = $this->getLeftStartPos($text, $font, $size, self::TEXT_ALIGN_LEFT);
            $pageContentWidth = $this->getPageContentWidth();

            $wordsArr        = explode(" ", $text);
            $wordsAmount     = count($wordsArr);
            $wordsTotalLengh = 0;

            foreach ($wordsArr as $word) {
                $wordsTotalLengh += $this->widthForStringUsingFontSize($word, $font, $size);
            }

            $space = ($pageContentWidth - $wordsTotalLengh) / (($wordsAmount > 1 ? $wordsAmount : 2) - 1);
            foreach ($wordsArr as $word) {
                $this->currentPage->setStyle($style)->drawText($word, $startPosInLine, $topYPosition);
                $startPosInLine += ($this->widthForStringUsingFontSize($word, $font, $size) + $space);
            }
        }

        return $topYPosition;
    }

    protected function drawImagesInColumns($topPosInPage, $images = array(), $columns = 3, $crops = true)
    {
        if (is_array($images) && !empty($images)) {
            if (is_integer($columns) < 1) {
                $columns = 1;
            } elseif (!is_integer($columns)) {
                $columns = 1;
            }

            $pageContentWidth  = $this->getPageContentWidth();
            $pageContentHeight = $this->getPageContentHeight();

            $newWidthForImage = $pageContentWidth / $columns;

            $xOffset = 0;
            $yPos    = $topPosInPage;
            foreach ($images as $counter => $image) {

                $position = $this->drawImage($image, $yPos, self::TEXT_ALIGN_LEFT, $newWidthForImage, 0, $xOffset, $crops);
                if ((($counter + 1) % $columns) == 0) {
                    $xOffset = 0;
                    $yPos    = $position['endYPos'];
                } else {
                    $xOffset += $newWidthForImage;
                    if ($position['endYPos'] > $yPos) { // if new page

                        $logo          = getimagesize($image);
                        $logoWidth     = $logo[0];
                        $logoHeight    = $logo[1];
                        $logoNewWidth  = $logoWidth;
                        $logoNewHeight = $logoHeight;

                        if ($logoWidth > $newWidthForImage) {
                            $scale         = $newWidthForImage / $logoWidth;
                            $logoNewWidth  = ceil($logoWidth * $scale);
                            $logoNewHeight = ceil($logoHeight * $scale);
                        }
                        $yPos = $position['startYPos'];
                    }
                }

                if ($position['endYPos'] > $yPos) {
                    $yPos = $position['startYPos'];
                }

                $topPosInPage = $position['endYPos'];
            }
        }

        return $topPosInPage;
    }

    protected function drawImage($logoPath, $topPosInPage, $position = self::TEXT_ALIGN_CENTER, $newImW = 0, $newImH = 0, $xOffset = 0, $crop = false)
    {

        $thumbLogoPath = null;

        $pageContentWidth  = $this->getPageContentWidth();
        $pageContentHeight = $this->getPageContentHeight();

        if ((is_integer($newImW) || is_float($newImW)) && ($newImW > 1) && ($newImW < $pageContentWidth)) {
            // ToDo
            // it's OK )
        } else {
            $newImW = 0;
        }

        if ((is_integer($newImH) || is_float($newImH)) && ($newImH > 1) && ($newImH < $pageContentHeight)) {
            // ToDo
            // it's OK )
        } else {
            $newImH = 0;
        }

        if ($crop) {
            if ($newImW > 0 && !$newImH) {
                $newImH = $newImW * 2 / 3;
            }

            if ($newImH > 0 && !$newImW) {
                $newImW = $newImH * 3 / 2;
            }
        }

        $logo       = ZendPdf\Image::imageWithPath($logoPath);
        $logoWidth  = $logo->getPixelWidth();
        $logoHeight = $logo->getPixelHeight();

        if ($newImW && $newImH && $crop) {

            // create tmp folder for image
            $tmpFolderPath = $this->get_pdf_tmp_path();
            $thumbLogoPath = str_replace(dirname($logoPath), $tmpFolderPath, $logoPath);
            $thumbLogoPath = preg_replace('/(\.[^\.]*)$/isU', "_crop$1", $thumbLogoPath);

            if (($logoWidth / $logoHeight) >= ($newImW / $newImH)) {
                $logoNewWidth  = $logoWidth / ($logoHeight / $newImH);
                $logoNewHeight = $newImH;
            } else {
                $logoNewWidth  = $newImW;
                $logoNewHeight = $logoHeight / ($logoWidth / $newImW);
            }
            $dst_x   = 0 - ($logoNewWidth - $newImW) / 2;
            $dst_y   = 0 - ($logoNewHeight - $newImH) / 2;
            $new_img = imagecreatetruecolor($newImW, $newImH);

            switch (strtolower(substr(strrchr($logoPath, '.'), 1))) {
                case 'jpg':
                case 'jpeg':
                    $src_img       = imagecreatefromjpeg($logoPath);
                    $write_image   = 'imagejpeg';
                    $image_quality = isset($options['jpeg_quality']) ? $options['jpeg_quality'] : 95;
                    break;
                case 'gif':
                    imagecolortransparent($new_img, imagecolorallocate($new_img, 0, 0, 0));
                    $src_img       = imagecreatefromgif($logoPath);
                    $write_image   = 'imagegif';
                    $image_quality = null;
                    break;
                case 'png':
                    imagecolortransparent($new_img, imagecolorallocate($new_img, 0, 0, 0));
                    imagealphablending($new_img, false);
                    imagesavealpha($new_img, true);
                    $src_img       = imagecreatefrompng($logoPath);
                    $write_image   = 'imagepng';
                    $image_quality = isset($options['png_quality']) ? $options['png_quality'] : 9;
                    break;
                default:
                    imagedestroy($new_img);

                    return false;
            }
            $success = imagecopyresampled($new_img, $src_img, $dst_x, $dst_y, 0, 0, $logoNewWidth, $logoNewHeight, $logoWidth, $logoHeight) && $write_image($new_img, $thumbLogoPath, $image_quality);

            // Free up memory (imagedestroy does not delete files):
            imagedestroy($src_img);
            imagedestroy($new_img);

            if (!file_exists($thumbLogoPath)) {
                /*$imProc = new ImageProcessor($logoPath);
                $imProc->crop(array('width' => $logoNewWidth, 'height' => $logoNewHeight, "start"=>array("x"=>$dst_x,"y"=>$dst_y)));
                $imProc->save($thumbLogoPath);*/
            }

            $logo       = ZendPdf\Image::imageWithPath($thumbLogoPath);
            $logoWidth  = $logo->getPixelWidth();
            $logoHeight = $logo->getPixelHeight();
        } elseif ($logoWidth > ($newImW ? $newImW : $pageContentWidth) || $logoHeight > $pageContentHeight) {
            $scale         = min(($newImW ? $newImW : $pageContentWidth) / $logoWidth, $pageContentHeight / $logoHeight);
            $logoNewWidth  = floor($logoWidth * $scale);
            $logoNewHeight = floor($logoHeight * $scale);

            // create tmp folder for image
            $tmpFolderPath = $this->get_pdf_tmp_path();
            $thumbLogoPath = str_replace(dirname($logoPath), $tmpFolderPath, $logoPath);
            $thumbLogoPath = preg_replace('/(\.[^\.]*)$/isU', "_{$logoNewWidth}x{$logoNewHeight}$1", $thumbLogoPath);
            if (!file_exists($thumbLogoPath)) {
                $imProc = new ImageProcessor($logoPath);
                $imProc->contain(array('width'  => $logoNewWidth,
                    'height' => $logoNewHeight
                ));
                $imProc->save($thumbLogoPath);
            }

            $logo       = ZendPdf\Image::imageWithPath($thumbLogoPath);
            $logoWidth  = $logo->getPixelWidth();
            $logoHeight = $logo->getPixelHeight();
        }

        $topPosInPage = $this->detectNewPage($topPosInPage -= ($logoHeight + self::BLOCK_TEXT_LINE_SPACING_AT), $logoHeight);

        $posInfo              = array();
        $posInfo['startYPos'] = $topPosInPage + ($logoHeight + self::BLOCK_TEXT_LINE_SPACING_AT);

        switch ($position) {
            case self::TEXT_ALIGN_RIGHT:
                $leftPos = self::PAGE_PADDING_LEFT + $pageContentWidth - $logoWidth;
                break;
            case self::TEXT_ALIGN_CENTER:
                $leftPos = self::PAGE_PADDING_LEFT + (($pageContentWidth - $logoWidth) / 2);
                break;
            case self::TEXT_ALIGN_LEFT:
            default:
                $leftPos = self::PAGE_PADDING_LEFT + $xOffset;
                break;
        }

        $this->currentPage->drawImage($logo, $leftPos, $topPosInPage, $leftPos + $logoWidth, $topPosInPage + $logoHeight);
        $topPosInPage -= self::BLOCK_TEXT_LINE_SPACING_AT;

        $posInfo['endYPos'] = $topPosInPage;

        if ($thumbLogoPath !== null && is_string($thumbLogoPath)) {
            if (file_exists($thumbLogoPath)) {
                unlink($thumbLogoPath);
                if (is_dir($dir_name = dirname($thumbLogoPath))) {
                    // scan folder
                    $flags    = \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS;
                    $iterator = new \RecursiveDirectoryIterator($dir_name, $flags);
                    $iterator = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST, \RecursiveIteratorIterator::CATCH_GET_CHILD);
                    if (iterator_count($iterator) == 0) {
                        rmdir($dir_name);
                    } else {
                        // clean and remove tmp dir
                        // foreach($iterator as $path) {
                        //     $path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
                        // }
                        // rmdir($dir_name);
                    }
                }
            }
        }

        return $posInfo;
    }

    protected function detectNewPage($startYPosition, $yOffset = 0)
    {

        if ($startYPosition <= self::PAGE_PADDING_BOTTOM) {
            $startYPosition = $this->createNewPage($yOffset);
        }

        return $startYPosition;
    }

    protected function createNewPage($yOffset = 0)
    {

        if (!empty($this->currentPage)) {
            $this->drawFooter();
            $this->document->pages[] = $this->currentPage;
        }

        $this->currentPage = new ZendPdf\Page(ZendPdf\Page::SIZE_A4);

        if ($drawHeader = false) {
            $pageYPosition = $this->drawHeader();
        } else {
            $pageYPosition = $this->getPageHeight();
            $pageYPosition -= self::PAGE_PADDING_TOP;
        }

        $pageYPosition -= $yOffset;

        return $pageYPosition;
    }

    protected function getLeftStartPos($string, $font, $fontSize, $position = self::TEXT_ALIGN_LEFT)
    {

        if (!is_string($string) || empty($string)) {
            throw new \Exception('Invalid string type or empty');
        }

        if (ceil((float) $fontSize) <= 0) {
            throw new \Exception('Invalid ZendPdf Font size');
        }

        $pageContentWidth = $this->getPageContentWidth();
        $strWidth         = $this->widthForStringUsingFontSize($string, $font, $fontSize);

        switch ($position) {
            case self::TEXT_ALIGN_RIGHT:
                return self::PAGE_PADDING_LEFT + $pageContentWidth - $strWidth;
                break;
            case self::TEXT_ALIGN_CENTER:
                return self::PAGE_PADDING_LEFT + (($pageContentWidth - $strWidth) / 2);
                break;
            case self::TEXT_ALIGN_LEFT:
            default:
                return self::PAGE_PADDING_LEFT;
                break;
        }
    }

    protected function getPageWidth()
    {

        if (!empty($this->currentPage)) {
            return $this->currentPage->getWidth();
        }

        return 0;
    }

    protected function getPageHeight()
    {

        if (!empty($this->currentPage)) {
            return $this->currentPage->getHeight();
        }

        return 0;
    }

    protected function getPageContentWidth()
    {
        return $this->getPageWidth() - self::PAGE_PADDING_LEFT - self::PAGE_PADDING_RIGHT;
    }

    protected function getPageContentHeight()
    {
        return $this->getPageHeight() - self::PAGE_PADDING_TOP - self::PAGE_PADDING_BOTTOM;
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
     * @param string                 $string
     * @param Zend_Pdf_Resource_Font $font
     * @param float                  $fontSize Font size in points
     *
     * @return float
     */
    protected function widthForStringUsingFontSize($string, $font, $fontSize)
    {
        $drawingString = iconv('UTF-8', 'UTF-16BE//IGNORE', $string);
        $characters    = array();
        for ($i = 0; $i < strlen($drawingString); $i++) {
            $characters[] = (ord($drawingString[$i++]) << 8) | ord($drawingString[$i]);
        }
        $glyphs      = $font->glyphNumbersForCharacters($characters);
        $widths      = $font->widthsForGlyphs($glyphs);
        $stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;

        return $stringWidth;
    }

    protected function get_filter_path($fEndPath = null)
    {
        if ($fEndPath === null || !is_string($fEndPath)) {
            $fEndPath = $this->uploadPath;
        }

        $root     = $this->get_root_path();
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

    protected function get_server_var($id)
    {
        return isset($_SERVER[$id]) ? $_SERVER[$id] : '';
    }

    protected function get_root_path()
    {
        $root = $this->get_server_var('DOCUMENT_ROOT');
        // check root
        if (!file_exists($root . "/init_autoloader.php")) {
            $root = dirname($root);
        }

        return $root;
    }

    protected function get_upload_path()
    {
        return $this->check_dir($this->get_root_path() . $this->get_filter_path());
    }

    /* not actual > * /
    protected function get_user_path() {
        $userPath = "";
        if(!empty($this->checkList)) {
            if(($user = $this->checkList->getUser()) !== null) {
                $userPath .= $user->getId();
                $userPath .= '/';
            }
        }

        return $this->check_dir($this->get_upload_path() . $userPath);
    }
    /* > end. */

    public function get_pdf_path()
    {
        return $this->check_dir($this->get_upload_path() . 'pdf/');
    }

    protected function get_pdf_tmp_path()
    {
        return $this->check_dir($this->get_pdf_path() . 'tmp/');
    }

    protected function get_name()
    {

        $name = $this->opts['name'];
        $ext  = !empty($this->opts['ext']) ? $this->opts['ext'] : '.pdf';

        $checkList = "0";
        $user      = "0";
        $vehicle   = "0";
        $date      = $this->dateGeneration->format('Y-m-d');

        if (!empty($this->checkList)) {
            $checkList = $this->checkList->getId();
            if (($clUser = $this->checkList->getUser()) !== null) {
                $user = $clUser->getId();
            }
            if (($clVehicle = $this->checkList->getVehicle()) !== null) {
                $vehicle = $clVehicle->getId();
            }
        }

        $templateFormat = $this->opts['template_for_name']['format'];
        $template       = $this->opts['template_for_name']['template'];
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

    protected function get_full_name()
    {
        return $this->get_pdf_path() . $this->get_name();
    }
}
