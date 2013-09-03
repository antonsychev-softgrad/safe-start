<?php namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use ZendPdf;
use SafeStartApi\Model\ImageProcessor;

class PdfPlugin extends AbstractPlugin {

    const PAGE_PADDING_LEFT = 58;
    const PAGE_PADDING_TOP = 24;
    const PAGE_PADDING_RIGHT = 58;
    const PAGE_PADDING_BOTTOM = 58;

    const PAGE_HEADER_TITLE_SIZE = 22;

    const BLOCK_PADDING_TOP = 25;
    const BLOCK_PADDING_BOTTOM = 20;
    const BLOCK_HEADER_SIZE = 19;
    const BLOCK_SUBHEADER_SIZE = 16;
    const BLOCK_SUBHEADER_COLOR_LINE_SIZE = 36;
    const BLOCK_SUBHEADER_COLOR_LINE_PADDING_BOTTOM = 13;
    const BLOCK_TEXT_SIZE = 12;
    const BLOCK_TEXT_LINE_SPACING_BEFORE = 5;
    const BLOCK_TEXT_LINE_SPACING_AFTER = 5;
    const BLOCK_TEXT_LINE_SPACING_AT = 2;

    const TEXT_ALIGN_LEFT = 'left';
    const TEXT_ALIGN_RIGHT = 'right';
    const TEXT_ALIGN_CENTER = 'center';
    const TEXT_ALIGN_JUSTIFY = 'justify';


    protected $document;
    protected $currentPage;
    protected $font;
    protected $checkList;
    protected $uploadPath;
    protected $docHeaderTitle = 'checklist review';
    protected $namePrefix = 'checklist_review';
    protected $dateGeneration;


    public function __invoke($checkListId = null) {

        if ($checkListId === null) {
            return $this;
        }

        return $this->create($checkListId);
    }


    public function create($checkListId = null) {


        /* test data > */
        $vehicleDetails = array(
            array(
                'name' => 'Safety',
                'status' => 'ok',
                ),
            array(
                'name' => 'Cabin',
                'status' => 'alert',
                ),
            array(
                'name' => 'Structural',
                'status' => 'ok',
                ),
            array(
                'name' => 'Mechanical',
                'status' => 'ok',
                ),
            array(
                'name' => 'Trailer',
                'status' => 'ok',
                ),
            array(
                'name' => 'Auxiliary Motor',
                'status' => 'ok',
                ),


            array(
                'name' => 'Safety',
                'status' => 'ok',
                ),
            array(
                'name' => 'Cabin',
                'status' => 'alert',
                ),
            array(
                'name' => 'Structural',
                'status' => 'ok',
                ),
            array(
                'name' => 'Mechanical',
                'status' => 'ok',
                ),
            array(
                'name' => 'Trailer',
                'status' => 'ok',
                ),
            array(
                'name' => 'Auxiliary Motor',
                'status' => 'ok',
                ),


            array(
                'name' => 'Safety',
                'status' => 'ok',
                ),
            array(
                'name' => 'Cabin',
                'status' => 'alert',
                ),
            array(
                'name' => 'Structural',
                'status' => 'ok',
                ),
            array(
                'name' => 'Mechanical',
                'status' => 'ok',
                ),
            array(
                'name' => 'Trailer',
                'status' => 'ok',
                ),
            array(
                'name' => 'Auxiliary Motor',
                'status' => 'ok',
                ),
            );


        $alertsDetails = array(
            array(
                'title' => 'Are the tires correctly inflated, in good working order and with wheel nuts tigh t e ned ?',
                'comment' => '',
                'images' => array(
                    ),
                ),
            array(
                'title' => 'Maecena nec',
                'comment' => 'Elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. adhjsf aisdgdhask ghkjdashkjgh kaghk fskdgj kdfjhsgkdf hskdgfkhs kgdfksghkg hdfkshgkj dhsgfkhkjdshgk dfskghdfkjs kghdfks ghkdhfskg hdlskhg kdsgkh ksdf hgdfks gkhdfk gkhdfs gfkd hsl gsdghlk sdaglksdfhg kjdfsgkdshfgk jdfskgl kdfsgkj dhfskhglkjdfshlgkh dfksgkh dfkjshgkjdf hgkhkjdf kjgk dhfkjgkjdfjkg kdfhgk kdfhkgjfhghgh gh hg hg gkhdfk kd kd k gkhdfkgh kdhgkh dfkg dfkgdfk hgkdfhgkdfgh dfgkdfh kgdhfkg dkg',
                'images' => array(
                    ),
                ),
            array(
                'title' => 'Cras dapibus',
                'comment' => 'Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet.',
                'images' => array(),
                ),
            array(
                'title' => 'Maecena nec',
                'comment' => 'Elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet.',
                'images' => array(),
                ),
            array(
                'title' => 'Cras dapibus',
                'comment' => 'Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet.',
                'images' => array(),
                ),
            array(
                'title' => 'Maecena nec',
                'comment' => 'Elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet.',
                'images' => array(
                    ),
                ),
            array(
                'title' => 'Cras dapibus',
                'comment' => 'Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet.',
                'images' => array(),
                ),
            array(
                'title' => 'Maecena nec',
                'comment' => 'Elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet.',
                'images' => array(),
                ),
            );
        /* > end test data. */

        $moduleConfig = $this->getController()->getServiceLocator()->get('Config');
        $fontPath = dirname(__file__) . "/../../../../public/fonts/HelveticaNeueLTStd-Cn.ttf";

        $this->document = new ZendPdf\PdfDocument();
        $this->currentPage = new ZendPdf\Page(ZendPdf\Page::SIZE_A4);
        //$this->font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_HELVETICA);
        $this->font = ZendPdf\Font::fontWithPath($fontPath);
        $this->uploadPath = $moduleConfig['defUsersPath'];
        $this->dateGeneration = date_create();

        /** /
        $this->checkList = $this->getController()->em->find('SafeStartApi\Entity\CheckList', $checkListId);
        if($this->checkList === null) {
            throw new \Exception('CheckList not found.');
        }
        $this->dateGeneration = new \DateTime($this->checkList->getCreationDate());

        $vehicleDetails = array();
        $alertsDetails = array();
        $fieldsStruct = json_decode($this->checkList->getFieldsStructure());
        foreach ($fieldsStruct as $groupBlock) {

            $group = new \stdClass();
            $group->name = $groupBlock['name'];
            $group->alerts = array();

            // get field id`s
            $filds = $groupBlock['fields'];
            $fieldIds = array();
            foreach ($filds as $fild) {
                $fieldIds[] = $fild['id'];
            }

            // get alerts
            if (!empty($fieldIds)) {
                $query = $this->getController()->em->createQuery('SELECT u FROM SafeStartApi\Entity\Alert u WHERE u.id IN (:ids)');
                $query->setParameters(array('ids' => implode(',', $fieldIds), ));
                $group->alerts = $query->getResult();
                if (is_array($group->alerts) && !empty($group->alerts)) {
                    foreach ($group->alerts as $alert) {
                        $alertInfo = new \stdClass();
                        $alertInfo->title = $alert->getField()->getAlertTitle();
                        $alertInfo->comment = $alert->getComment();

                        $images = array();
                        $imagesInfo = $alert->getImages();
                        if($imagesInfo !== null) {
                            $imagesInfo = json_decode($imagesInfo);
                            if(is_array($imagesInfo) && !empty($imagesInfo)) {
                                foreach($imagesInfo as $imageName) {
                                    if(($imagePath = $this->getImagePathByName($imageName)) !== null) {
                                        $images[] = $imagePath;
                                    }
                                }
                            }
                        }
                        $alertInfo->images = $images;
                        $alertsDetails[] = $alertInfo;
                    }
                }
            }

            // set status
            $group->status = empty($group->alerts) ? 'ok' : 'alert';
            $vehicleDetails[] = $group;
        }
        /**/

        // header >
        $topPosInPage = $this->drawHeader();
        // draw vehicle details block >
        $topPosInPage = $this->drawTextBlock('vehicle', 'vehicle details', $vehicleDetails, $topPosInPage);

        // draw alerts block >
        $topPosInPage = $this->drawTextBlock('alerts', 'alerts', $alertsDetails, $topPosInPage);

        $this->document->pages[] = $this->currentPage;


        $file_name = $this->get_name();
        $full_name = $this->get_full_name();
        $this->document->save($full_name);

        /**/
        header("Content-Disposition: inline; filename={$file_name}");
        header("Content-type: application/x-pdf");
        echo file_get_contents($full_name);
        /**/

        return $full_name;
    }

    protected function getFileByDirAndName($dir, $tosearch) {
        if(file_exists($dir) && is_dir($dir)) {
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
        }
        return null;
    }

    protected function getImagePathByName($fileName) {
        $filePath = $this->get_user_path();
        $fileName = "{$fileName}";
        if(($file = self::getFileByDirAndName($filePath, $fileName)) !== null) {
            $fileSizeInfo = @getimagesize($file);
            if($fileSizeInfo) { // it`s image
                return $file;
            }
        }

        return null;
    }


    protected function drawHeader() {

        $pageHeight = $this->getPageHeight();
        $pageWidth = $this->getPageWidth();

        // draw logo image >
        $root = $this->get_root_path();

        $logoMaxWidth = 199;
        $logoMaxHeight = 53;
        $logoPath = "{$root}/module/SafeStartApp/public/resources/img/logo.png";

        $thumbNewWidth = $logoMaxWidth * 2;
        $thumbNewHeight = $logoMaxHeight * 2;

        $thumbLogoPath = preg_replace('/(\.[^\.]*)$/isU', "_{$thumbNewWidth}x{$thumbNewHeight}$1", $logoPath);
        if (!file_exists($thumbLogoPath)) {
            $imProc = new ImageProcessor($logoPath);
            $imProc->contain(array('width' => $thumbNewWidth, 'height' => $thumbNewHeight));
            $imProc->save($thumbLogoPath);
        }

        $logo = ZendPdf\Image::imageWithPath($thumbLogoPath);
        $logoWidth = $logo->getPixelWidth();
        $logoHeight = $logo->getPixelHeight();

        $scale = min($logoMaxWidth / $logoWidth, $logoMaxHeight / $logoHeight);
        $logoNewWidth = (int)($logoWidth * $scale);
        $logoNewHeight = (int)($logoHeight * $scale);

        $this->currentPage->drawImage($logo, 56, $pageHeight - 24 - $logoNewHeight, 56 + $logoNewWidth, $pageHeight - 24);
        // > end draw logo image.

        // draw header title >
        $text = strtoupper($this->docHeaderTitle);
        $this->drawText($text, self::PAGE_HEADER_TITLE_SIZE, '#0F5B8D', $pageHeight - 60, self::TEXT_ALIGN_RIGHT);
        // > end draw header title.

        // draw header line >
        $style = new ZendPdf\Style();
        $lineColor = ZendPdf\Color\Html::color('#FFEB40');
        $style->setLineColor($lineColor);
        $style->setFillColor($lineColor);
        $style->setLineWidth(1);
        $this->currentPage->setStyle($style)->drawLine(0, $pageHeight - 102, $pageWidth, $pageHeight - 102);

        $style = new ZendPdf\Style();
        $lineColor = ZendPdf\Color\Html::color('#FEE401');
        $style->setLineColor($lineColor);
        $style->setFillColor($lineColor);
        $style->setLineWidth(1);
        $this->currentPage->setStyle($style)->drawLine(0, $pageHeight - 103, $pageWidth, $pageHeight - 103);

        $style = new ZendPdf\Style();
        $lineColor = ZendPdf\Color\Html::color('#FEFAD3');
        $style->setLineColor($lineColor);
        $style->setFillColor($lineColor);
        $style->setLineWidth(1);
        $this->currentPage->setStyle($style)->drawLine(0, $pageHeight - 104, $pageWidth, $pageHeight - 104);
        // > end draw header line.

        return ($pageHeight -= 105);
    }


    protected function drawTextBlock($type, $headerTitle, $params, $topPosInPage) {

        if (is_array($params) && !empty($params)) {
            $topPosInPage -= self::BLOCK_PADDING_TOP;

            switch (strtolower($type)) {
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


    protected function drawVehicleBlock($headerTitle, $params, $topPosInPage) {

        $pageWidth = $this->getPageWidth();

        $topPosInPage -= self::BLOCK_HEADER_SIZE;
        $text = ucfirst($headerTitle);
        $topPosInPage = $this->drawText($text, self::BLOCK_HEADER_SIZE, '#0F5B8D', $topPosInPage);

        $lineCounter = 0;
        $topPosInPage += 8;
        $topPosInPage -= (self::BLOCK_SUBHEADER_COLOR_LINE_SIZE + self::BLOCK_SUBHEADER_COLOR_LINE_PADDING_BOTTOM);
        foreach ($params as $group) {

            $drawLine = (bool)(++$lineCounter % 2);
            $fLinePos = $topPosInPage;

            if(is_array($group) && !empty($group)) {
                $title = $group['name'];
                $status = $group['status'];
            } elseif($group instanceof \stdClass) {
                $title = $group->name;
                $status = $group->status;
            } else {
                continue;
            }

            $title = strip_tags($title);
            $title = ucwords($title);
            $headlineArray = $this->getTextLines($title, self::BLOCK_SUBHEADER_SIZE);
            $subLineCounter = 0;
            foreach ($headlineArray as $line) {
                if ($drawLine) {
                    // first color
                }
                else {
                    // second color
                    $lineStartYPos = $topPosInPage - self::BLOCK_SUBHEADER_COLOR_LINE_PADDING_BOTTOM;
                    $topPosInPage = $lineStartYPos = $this->detectNewPage($lineStartYPos, self::BLOCK_SUBHEADER_COLOR_LINE_SIZE);
                    $topPosInPage += self::BLOCK_SUBHEADER_COLOR_LINE_PADDING_BOTTOM;

                    $lineColor = ZendPdf\Color\Html::color('#EBEBEB');
                    $lineStyle = new ZendPdf\Style();
                    $lineStyle->setLineColor($lineColor);
                    $lineStyle->setFillColor($lineColor);
                    $this->currentPage->setStyle($lineStyle)->drawRectangle(0, $lineStartYPos, $pageWidth, $lineStartYPos + self::BLOCK_SUBHEADER_COLOR_LINE_SIZE);
                }

                $text = trim($line);
                $topPosInPage = $this->drawText($text, self::BLOCK_SUBHEADER_SIZE, '#333333', $topPosInPage);

                if (!$subLineCounter++) {
                    $fLinePos = $topPosInPage;
                }

                $topPosInPage -= self::BLOCK_SUBHEADER_COLOR_LINE_SIZE;
            }

            // draw status >
            $status = strtoupper($status);
            switch (strtolower($status)) {
                case 'alert':
                    $color = "#ff0000";
                    break;
                case 'ok':
                default:
                    $color = "#0f5b8d";
                    break;
            }
            $this->drawText($status, self::BLOCK_SUBHEADER_SIZE, $color, $fLinePos, self::TEXT_ALIGN_RIGHT);
            // > end draw status.

        }

        return $topPosInPage;
    }


    protected function drawAlertsBlock($headerTitle, $params, $topPosInPage) {

        $topPosInPage -= self::BLOCK_HEADER_SIZE;
        $text = ucfirst($headerTitle);
        $topPosInPage = $this->drawText($text, self::BLOCK_HEADER_SIZE, '#ff0000', $topPosInPage);

        foreach ($params as $alertInfo) {

            if(is_array($alertInfo) && !empty($alertInfo)) {
                $title = $alertInfo['title'];
                $comment = $alertInfo['comment'];
                $images = $alertInfo['images'];
            } elseif($alertInfo instanceof \stdClass) {
                $title = $alertInfo->title;
                $comment = $alertInfo->comment;
                $images = $alertInfo->images;
            } else {
                continue;
            }

            $topPosInPage -= (self::BLOCK_SUBHEADER_COLOR_LINE_SIZE + self::BLOCK_SUBHEADER_COLOR_LINE_PADDING_BOTTOM);

            $title = strip_tags($title);
            $headlineArray = $this->getTextLines($title, self::BLOCK_SUBHEADER_SIZE);
            $lineCounter = count($headlineArray);
            foreach ($headlineArray as $line) {
                $text = trim($line);
                $topPosInPage = $this->drawText($text, self::BLOCK_SUBHEADER_SIZE, '#ff0000', $topPosInPage);
                if ((--$lineCounter) > 0) {
                    $topPosInPage -= (self::BLOCK_SUBHEADER_SIZE + self::BLOCK_TEXT_LINE_SPACING_AT);
                }
                else {
                    $topPosInPage -= self::BLOCK_SUBHEADER_COLOR_LINE_SIZE;
                }
            }

            if(!empty($comment) && is_string($comment)) {
                $comment = strip_tags($comment);
                $headlineArray = $this->getTextLines($comment, self::BLOCK_TEXT_SIZE);
                $lineCounter = count($headlineArray);
                foreach ($headlineArray as $line) {
                    $text = trim($line);
                    $topPosInPage = $this->drawText($text, self::BLOCK_TEXT_SIZE, '#333333', $topPosInPage);
                    $topPosInPage -= self::BLOCK_TEXT_SIZE;
                    if ((--$lineCounter) > 0) {
                        $topPosInPage -= self::BLOCK_TEXT_LINE_SPACING_AT;
                    }
                }
            }

            if(isset($images)) {
                if(is_array($images) && !empty($images)) {
                    foreach($images as $image) {
                        $topPosInPage = $this->drawImage($image, $topPosInPage);
                    }
                }
            }
        }

        return $topPosInPage;
    }

    protected function getTextLines($text, $size, $font = null, $maxStrWidth = null) {

        if ($font === null) {
            $font = $this->font;
        }

        $pageContentWidth = $this->getPageContentWidth();
        if($maxStrWidth === null || !is_int($maxStrWidth)) {
            $maxStrWidth = $pageContentWidth;
        } elseif(is_int($maxStrWidth)) {
            if($maxStrWidth > $pageContentWidth) {
                $maxStrWidth = $pageContentWidth;
            }
        }

        $returnLines = array();
        $textLines = explode("\n", $text);
        foreach($textLines as $line) {
            $newLine = "";
            $tmpLine = "";
            $tLine = trim(preg_replace("/\s+/is", " ", $line));
            $tLineWordsArr = wordwrap($tLine, 1, "\n");
            $tLineWordsArr = explode("\n", $tLineWordsArr);
            foreach($tLineWordsArr as $word) {
                $tmpLine .= " " . $word;
                $tmpLine = trim($tmpLine);
                $strWidth = $this->widthForStringUsingFontSize($tmpLine, $font, $size);
                if($strWidth > $maxStrWidth) {
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

    protected function drawText($text, $size, $color, $topYPosition, $align = self::TEXT_ALIGN_LEFT, $font = null) {

        if ($font === null) {
            $font = $this->font;
        }

        $color = ZendPdf\Color\Html::color($color);
        $style = new ZendPdf\Style();
        $style->setFillColor($color);
        $style->setFont($font, $size);

        $topYPosition = $this->detectNewPage($topYPosition, $size);
        if($align !== self::TEXT_ALIGN_JUSTIFY) {
            $leftPosInStr = $this->getLeftStartPos($text, $font, $size, $align);
            $this->currentPage->setStyle($style)->drawText($text, $leftPosInStr, $topYPosition);
        } else {

            $startPosInLine = $this->getLeftStartPos($text, $font, $size, self::TEXT_ALIGN_LEFT);
            $pageContentWidth = $this->getPageContentWidth();

            $wordsArr = explode(" ", $text);
            $wordsAmount = count($wordsArr);
            $wordsTotalLengh = 0;

            foreach($wordsArr as $word) {
                $wordsTotalLengh += $this->widthForStringUsingFontSize($word, $font, $size);
            }

            $space = ($pageContentWidth - $wordsTotalLengh) / (($wordsAmount > 1 ? $wordsAmount : 2) - 1);
            foreach($wordsArr as $word) {
                $this->currentPage->setStyle($style)->drawText($word, $startPosInLine, $topYPosition);
                $startPosInLine += ($this->widthForStringUsingFontSize($word, $font, $size) + $space);
            }
        }

        return $topYPosition;
    }


    protected function drawImage($logoPath, $topPosInPage, $position = self::TEXT_ALIGN_CENTER) {

        $thumbLogoPath = null;

        $pageContentWidth = $this->getPageContentWidth();
        $pageContentHeight = $this->getPageContentHeight();

        $logo = ZendPdf\Image::imageWithPath($logoPath);
        $logoWidth = $logo->getPixelWidth();
        $logoHeight = $logo->getPixelHeight();

        if ($logoWidth > $pageContentWidth || $logoHeight > $pageContentHeight) {
            $scale = min($pageContentWidth / $logoWidth, $pageContentHeight / $logoHeight);
            $logoNewWidth = (int)($logoWidth * $scale);
            $logoNewHeight = (int)($logoHeight * $scale);

            // create tmp folder for image
            $tmpFolderPath = $this->get_pdf_tmp_path();
            $thumbLogoPath = str_replace(dirname($logoPath), $tmpFolderPath, $logoPath);
            $thumbLogoPath = preg_replace('/(\.[^\.]*)$/isU', "_{$logoNewWidth}x{$logoNewHeight}$1", $thumbLogoPath);
            if (!file_exists($thumbLogoPath)) {
                $imProc = new ImageProcessor($logoPath);
                $imProc->contain(array('width' => $logoNewWidth, 'height' => $logoNewHeight));
                $imProc->save($thumbLogoPath);
            }

            $logo = ZendPdf\Image::imageWithPath($thumbLogoPath);
            $logoWidth = $logo->getPixelWidth();
            $logoHeight = $logo->getPixelHeight();
        }

        $topPosInPage -= self::BLOCK_TEXT_LINE_SPACING_AT;
        $topPosInPage = $this->detectNewPage($topPosInPage -= $logoHeight, $logoHeight);
        switch ($position) {
            case self::TEXT_ALIGN_RIGHT:
                $leftPos = self::PAGE_PADDING_LEFT + $pageContentWidth - $logoWidth;
                break;
            case self::TEXT_ALIGN_CENTER:
                $leftPos = self::PAGE_PADDING_LEFT + (($pageContentWidth - $logoWidth) / 2);
                break;
            case self::TEXT_ALIGN_LEFT:
            default:
                $leftPos = self::PAGE_PADDING_LEFT;
                break;
        }

        $this->currentPage->drawImage($logo, $leftPos, $topPosInPage, $leftPos + $logoWidth, $topPosInPage + $logoHeight);
        $topPosInPage -= self::BLOCK_TEXT_LINE_SPACING_AT;

        if ($thumbLogoPath !== null && is_string($thumbLogoPath)) {
            if (file_exists($thumbLogoPath)) {
                unlink($thumbLogoPath);
                if (is_dir($dir_name = dirname($thumbLogoPath))) {
                    // scan folder
                    $flags = \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS;
                    $iterator = new \RecursiveDirectoryIterator($dir_name, $flags);
                    $iterator = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST, \RecursiveIteratorIterator::CATCH_GET_CHILD);
                    if (iterator_count($iterator) == 0) {
                        rmdir($dir_name);
                    }
                    else {
                        // clean and remove tmp dir
                        // foreach($iterator as $path) {
                        //     $path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
                        // }
                        // rmdir($dir_name);
                    }
                }
            }
        }

        return $topPosInPage;
    }


    protected function detectNewPage($startYPosition, $yOffset = 0) {

        if ($startYPosition <= self::PAGE_PADDING_BOTTOM) {
            $startYPosition = $this->createNewPage($yOffset);
        }

        return $startYPosition;
    }


    protected function createNewPage($yOffset = 0) {

        if (isset($this->currentPage)) {
            $this->document->pages[] = $this->currentPage;
        }

        $this->currentPage = new ZendPdf\Page(ZendPdf\Page::SIZE_A4);

        if ($drawHeader = false) {
            $pageYPosition = $this->drawHeader();
        }
        else {
            $pageYPosition = $this->getPageHeight();
        }

        $pageYPosition -= (self::PAGE_PADDING_TOP + $yOffset);

        return $pageYPosition;
    }


    protected function getLeftStartPos($string, $font, $fontSize, $position = self::TEXT_ALIGN_LEFT) {

        if (!is_string($string) || empty($string)) {
            throw new Zend\Exception('Invalid string type or empty');
        }

        if (ceil((float)$fontSize) <= 0) {
            throw new Zend\Exception('Invalid ZendPdf Font size');
        }

        $pageContentWidth = $this->getPageContentWidth();
        $strWidth = $this->widthForStringUsingFontSize($string, $font, $fontSize);

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


    protected function getPageWidth() {

        if (isset($this->currentPage)) {
            return $this->currentPage->getWidth();
        }

        return 0;
    }


    protected function getPageHeight() {

        if (isset($this->currentPage)) {
            return $this->currentPage->getHeight();
        }

        return 0;
    }


    protected function getPageContentWidth() {
        return $this->getPageWidth() - self::PAGE_PADDING_LEFT - self::PAGE_PADDING_RIGHT;
    }


    protected function getPageContentHeight() {
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
     * @param string $string
     * @param Zend_Pdf_Resource_Font $font
     * @param float $fontSize Font size in points
     * @return float
     */
    protected function widthForStringUsingFontSize($string, $font, $fontSize) {
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

    protected function get_filter_path($fEndPath = null) {
        if ($fEndPath === null || !is_string($fEndPath)) {
            $fEndPath = $this->uploadPath;
        }

        $root = $this->get_root_path();
        $fEndPath = str_replace("{$root}", '', $fEndPath);
        $fEndPath = str_replace('\\', '/', $fEndPath);

        if (preg_match('/^(\/|.\/).*/isU', $fEndPath, $match)) {
            $fEndPath = preg_replace('/^(\/|.\/).*/isU', "", $fEndPath);
        }
        else {
            $fEndPath = preg_replace('/^(.*)$/isU', "$1", $fEndPath);
        }

        $returnFolder = '/' . $fEndPath;
        if (!preg_match('/.*(\/)$/isU', $returnFolder, $match)) {
            $returnFolder .= '/';
        }

        return $returnFolder;
    }

    protected function check_dir($dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return $dir;
    }

    protected function get_server_var($id) {
        return isset($_SERVER[$id]) ? $_SERVER[$id] : '';
    }

    protected function get_root_path() {
        $root = $this->get_server_var('DOCUMENT_ROOT');
        // check root
        if (!file_exists($root . "/init_autoloader.php")) {
            $root = dirname($root);
        }

        return $root;
    }

    protected function get_upload_path() {
        return $this->check_dir($this->get_root_path() . $this->get_filter_path());
    }

    protected function get_user_path() {
        $userPath = "";
        if(isset($this->checkList)) {
            if(($user = $this->checkList->getUser()) !== null) {
                $userPath .= $user->getUser()->getId();
                $userPath .= '/';
            }
        }

        return $this->check_dir($this->get_upload_path() . $userPath);
    }

    protected function get_pdf_path() {
        return $this->check_dir($this->get_user_path() . 'pdf/');
    }

    protected function get_pdf_tmp_path() {
        return $this->check_dir($this->get_pdf_path() . 'tmp/');
    }

    protected function get_name() {
        return $this->namePrefix . '_at_' .$this->dateGeneration->format('Y-m-d') . '.pdf';
    }

    protected function get_full_name() {
        return $this->get_pdf_path() . $this->get_name();
    }
}
