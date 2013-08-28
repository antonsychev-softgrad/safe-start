<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestrictedAccessRestController;

class ProcessDataController extends RestrictedAccessRestController
{
    public function uploadImagesAction() {
        $this->answer = array(
            'uploadInfo' => $this->UploadPlugin(array('param_name'=>'image'))->post(),
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function generatePdfAction() {
        $this->PdfPlugin()->create();
    }
}