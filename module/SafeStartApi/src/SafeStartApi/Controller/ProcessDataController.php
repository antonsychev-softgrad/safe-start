<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestrictedAccessRestController;

class ProcessDataController extends RestrictedAccessRestController
{
    public function uploadImagesAction() {
        $return = array();
        if ($this->getRequest()->isPost()) {
            $return = $this->UploadPlugin(array('param_name'=>'files'))->post();
        }
        return $this->AnswerPlugin()->format($return);
    }

    public function generatePdfAction() {

    }
}