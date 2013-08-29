<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestrictedAccessRestController;

class ProcessDataController extends RestrictedAccessRestController
{
    public function uploadImagesAction() {
        $return = $this->UploadPlugin(array('param_name'=>'image'))->post();
        return $this->AnswerPlugin()->format($return);
    }

    public function generatePdfAction() {
        $this->PdfPlugin()->create();
    }
}