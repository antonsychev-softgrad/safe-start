<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestrictedAccessRestController;

class ProcessDataController extends RestrictedAccessRestController
{
    public function uploadImagesAction() {
        $return = null;
        $return = $this->UploadPlugin(array('param_name'=>'image'))->post();

        $this->answer = array(
            'uploadInfo' => $return,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function generatePdfAction() {
        $this->PdfPlugin()->create();
    }
}