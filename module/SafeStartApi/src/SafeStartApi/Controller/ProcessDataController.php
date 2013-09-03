<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestrictedAccessRestController;

class ProcessDataController extends RestrictedAccessRestController
{
    public function uploadImagesAction() {
        $this->answer = $this->UploadPlugin(array('param_name'=>'image'))->post();
        return $this->AnswerPlugin()->format($this->answer);
    }

    public function generatePdfAction() {
        if (($checkListId = (int)$this->params('id')) !== null) {
            $checkList = $this->em->find('SafeStartApi\Entity\CheckList', $checkListId);
            if ($checkList !== null) {
                $this->PdfPlugin($checkList->getId());
            } else {
                $this->answer = array(
                    "errorMessage" => "CheckList not found."
                );
                return $this->AnswerPlugin()->format($this->answer, 404);
            }
        } else {
            $this->_showBadRequest();
        }
    }
}