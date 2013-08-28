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
    const BLOCK_TEXT_LINE_SPACING = 2;

    const TEXT_ALIGN_LEFT = 'left';
    const TEXT_ALIGN_RIGHT = 'right';
    const TEXT_ALIGN_CENTER = 'center';

    protected $document;
    protected $currentPage;
    protected $font;
    protected $uploadPath; // automatic add '/userId/pdf/'
    protected $dateGeneration;


    public function __invoke($params = null) {
        if ($params === null) {
            return $this;
        }
        return $this->create($params);
    }

    public function create($params = null) {

        $vehicleDetails = array(
            array(
                'title' => 'Safety',
                'status' => 'ok',
                ),
            array(
                'title' => 'Cabin',
                'status' => 'alert',
                ),
            array(
                'title' => 'Structural',
                'status' => 'ok',
                ),
            array(
                'title' => 'Mechanical',
                'status' => 'ok',
                ),
            array(
                'title' => 'Trailer',
                'status' => 'ok',
                ),
            array(
                'title' => 'Auxiliary Motor',
                'status' => 'ok',
                ),



            array(
                'title' => 'Safety',
                'status' => 'ok',
                ),
            array(
                'title' => 'Cabin',
                'status' => 'alert',
                ),
            array(
                'title' => 'Structural',
                'status' => 'ok',
                ),
            array(
                'title' => 'Mechanical',
                'status' => 'ok',
                ),
            array(
                'title' => 'Trailer',
                'status' => 'ok',
                ),
            array(
                'title' => 'Auxiliary Motor',
                'status' => 'ok',
                ),



            array(
                'title' => 'Safety',
                'status' => 'ok',
                ),
            array(
                'title' => 'Cabin',
                'status' => 'alert',
                ),
            array(
                'title' => 'Structural',
                'status' => 'ok',
                ),
            array(
                'title' => 'Mechanical',
                'status' => 'ok',
                ),
            array(
                'title' => 'Trailer',
                'status' => 'ok',
                ),
            array(
                'title' => 'Auxiliary Motor',
                'status' => 'ok',
                ),
        );


        $alertsDetails = array(
            array(
                'subheader' => 'Are the tires correctly inflated, in good working order and with wheel nuts tightened?',
                'details' => array('Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet.'),
                ),
            array(
                'subheader' => 'Maecena nec',
                'details' => array('Elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet.'),
                ),
            array(
                'subheader' => 'Cras dapibus',
                'details' => array('Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet.'),
                ),
            array(
                'subheader' => 'Maecena nec',
                'details' => array('Elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet.'),
                ),
            array(
                'subheader' => 'Cras dapibus',
                'details' => array('Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet.'),
                ),
            array(
                'subheader' => 'Maecena nec',
                'details' => array('Elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet.'),
                ),
            array(
                'subheader' => 'Cras dapibus',
                'details' => array('Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet.'),
                ),
            array(
                'subheader' => 'Maecena nec',
                'details' => array('Elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet.'),
                ),
            );

        $moduleConfig = $this->getController()->getServiceLocator()->get('Config');
        $fontPath = dirname(__file__) . "/../../../../public/fonts/HelveticaNeueLTStd-Cn.ttf";

        $this->document = new ZendPdf\PdfDocument();
        $this->currentPage = new ZendPdf\Page(ZendPdf\Page::SIZE_A4);
        //$this->font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_HELVETICA);
        $this->font = ZendPdf\Font::fontWithPath($fontPath);
        $this->uploadPath = $moduleConfig['defUsersPath'];
        $this->dateGeneration = date_create();

        // header >
        $topPosInPage = $this->drawHeader('checklist review');
        // draw vehicle details block >
        $topPosInPage = $this->drawTextBlock('vehicle', 'vehicle details', $vehicleDetails, $topPosInPage);
        // draw alerts block >
        $topPosInPage = $this->drawTextBlock('alerts', 'alerts', $alertsDetails, $topPosInPage);

        $this->document->pages[] = $this->currentPage;

        $name = 'checklist_review';
        $file_name = $this->get_name($name);
        $full_name = $this->get_full_name($name);
        $this->document->save($full_name);

        /**/
        header("Content-Disposition: inline; filename={$file_name}");
        header("Content-type: application/x-pdf");
        echo file_get_contents($full_name);
        /**/

        return $full_name;
    }

    protected function drawHeader($headerTitle) {

        $pageHeight = $this->getPageHeight();
        $pageWidth = $this->getPageWidth();

        // draw logo image >
        $root = $this->get_server_var('DOCUMENT_ROOT');

        $logoMaxWidth = 199;
        $logoMaxHeight = 53;
        $logoPath = "{$root}/public/logo.png";

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
        $text = strtoupper($headerTitle);
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
        // draw alert block >
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
        // > end draw vehicle detailsblock.

        return $topPosInPage;
    }


    protected function drawVehicleBlock($headerTitle, $params, $topPosInPage) {

        $pageWidth = $this->getPageWidth();

        $topPosInPage -= self::BLOCK_HEADER_SIZE;
        $text = ucfirst($headerTitle);
        $topPosInPage = $this->drawText($text, self::BLOCK_HEADER_SIZE, '#0F5B8D', $topPosInPage);
        // > end draw draw vehicle details title.

        $lineCounter = 0;
        $topPosInPage += 8;
        $topPosInPage -= (self::BLOCK_SUBHEADER_COLOR_LINE_SIZE + self::BLOCK_SUBHEADER_COLOR_LINE_PADDING_BOTTOM);
        foreach ($params as $vehicleDetail) {

            $drawLine = (bool)(++$lineCounter % 2);
            $fLinePos = $topPosInPage;

            $title = $vehicleDetail['title'];
            $title = strip_tags($title);
            $title = wordwrap($title, 70, '\n');

            $headlineArray = explode('\n', $title);

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

                // draw subheader title >
                $text = trim($line);
                $topPosInPage = $this->drawText($text, self::BLOCK_SUBHEADER_SIZE, '#333333', $topPosInPage);

                if(!$subLineCounter++) {
                    $fLinePos = $topPosInPage;
                }

                $topPosInPage -= self::BLOCK_SUBHEADER_COLOR_LINE_SIZE;
                // > end draw subheader title.
            }

            // draw subheader status >
            $text = strtoupper($vehicleDetail['status']);
            switch (strtolower($text)) {
                case 'alert':
                    $color = "#ff0000";
                    break;
                case 'ok':
                default:
                    $color = "#0f5b8d";
                    break;
            }
            $this->drawText($text, self::BLOCK_SUBHEADER_SIZE, $color, $fLinePos, self::TEXT_ALIGN_RIGHT);
            // > end draw subheader status.

        }

        return $topPosInPage;
    }


    protected function drawAlertsBlock($headerTitle, $params, $topPosInPage) {

        $topPosInPage -= self::BLOCK_HEADER_SIZE;
        $text = ucfirst($headerTitle);
        $topPosInPage = $this->drawText($text, self::BLOCK_HEADER_SIZE, '#ff0000', $topPosInPage);
        // > end draw alert title.

        foreach ($params as $alertDeatails) {
            //$topPosInPage += 8;
            $topPosInPage -= (self::BLOCK_SUBHEADER_COLOR_LINE_SIZE + self::BLOCK_SUBHEADER_COLOR_LINE_PADDING_BOTTOM);

            $subHeader = $alertDeatails['subheader'];
            $subHeader = strip_tags($subHeader);
            $subHeader = wordwrap($subHeader, 85, '\n');

            $headlineArray = explode('\n', $subHeader);
            $lineCounter = count($headlineArray);
            foreach ($headlineArray as $line) {
                // draw subheader >
                $text = trim($line);
                $topPosInPage = $this->drawText($text, self::BLOCK_SUBHEADER_SIZE, '#ff0000', $topPosInPage);
                if ((--$lineCounter) > 0) {
                    $topPosInPage -= (self::BLOCK_SUBHEADER_SIZE + self::BLOCK_TEXT_LINE_SPACING);
                } else {
                    $topPosInPage -= self::BLOCK_SUBHEADER_COLOR_LINE_SIZE;
                }

                // > end draw subheader.
            }

            foreach ($alertDeatails['details'] as $detailMsg) {
                $detailMsg = strip_tags($detailMsg);
                $detailMsg = wordwrap($detailMsg, 110, '\n');

                $headlineArray = explode('\n', $detailMsg);
                $lineCounter = count($headlineArray);
                foreach ($headlineArray as $line) {
                    // draw subheader >
                    $text = trim($line);
                    $topPosInPage = $this->drawText($text, self::BLOCK_TEXT_SIZE, '#333333', $topPosInPage);
                    $topPosInPage -= self::BLOCK_TEXT_SIZE;
                    // > end draw subheader.
                    if ((--$lineCounter) > 0) {
                        $topPosInPage -= self::BLOCK_TEXT_LINE_SPACING;
                    }
                }
            }
        }

        return $topPosInPage;
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
        $leftPosInStr = $this->getLeftStartPos($text, $font, $size, $align);

        $this->currentPage->setStyle($style)->drawText($text, $leftPosInStr, $topYPosition);

        return $topYPosition;
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

        $pageContentWidth = $this->pageContentWidth();
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

    protected function pageContentWidth() {
        return $this->getPageWidth() - self::PAGE_PADDING_LEFT - self::PAGE_PADDING_RIGHT;
    }

    protected function pageContentHeight() {
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

    protected function get_server_var($id) {
        return isset($_SERVER[$id]) ? $_SERVER[$id] : '';
    }

    protected function get_user_id() {
        $user_folder = '';

        try {
            if (isset($this->getController()->authService)) {
                if ($this->getController()->authService->hasIdentity()) {
                    $user = $this->getController()->authService->getStorage()->read();
                    $user_folder = "" . $user->getId() . "/";
                }
            }
        }
        catch (\Exception $e) {

        }

        return $user_folder;
    }

    protected function get_full_path($fEndPath = '/') {
        $root = $this->get_server_var('DOCUMENT_ROOT');
        $fEndPath = str_replace("{$root}", '', $fEndPath);
        $fEndPath = str_replace('\\', '/', $fEndPath);

        if (preg_match('/^(\/|.\/).*/isU', $fEndPath, $match)) {
            $fEndPath = preg_replace('/^(\/|.\/).*/isU', "", $fEndPath);
        }
        else {
            $fEndPath = preg_replace('/^(.*)$/isU', "$1", $fEndPath);
        }

        $returnFolder = $root . '/' . $fEndPath;
        if (!preg_match('/.*(\/)$/isU', $returnFolder, $match)) {
            $returnFolder .= '/';
        }

        $returnFolder .= $this->get_user_id() . 'pdf/';

        if (!is_dir($returnFolder)) {
            mkdir($returnFolder, 0755, true);
        }

        return $returnFolder;
    }

    protected function get_full_url() {
        $https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0;
        return ($https ? 'https://' : 'http://') . (!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] . '@' : '') . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] :
            ($_SERVER['SERVER_NAME'] . ($https && $_SERVER['SERVER_PORT'] === 443 || $_SERVER['SERVER_PORT'] === 80 ? '' : ':' . $_SERVER['SERVER_PORT'])))
            //.substr($_SERVER['SCRIPT_FILENAME'],0, strrpos($_SERVER['SCRIPT_FILENAME'], '/'))
            ;
    }

    protected function get_name($name) {
        return $name . '_' . $this->dateGeneration->format('Y-m-d') . '.pdf';
    }

    protected function get_full_name($name) {
        return $this->get_full_path($this->uploadPath) . $this->get_name($name);
    }
}
