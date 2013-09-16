<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\PublicAccessRestController;

class ProcessDataController extends PublicAccessRestController
{
    public function uploadImagesAction() {
        $return = $this->UploadPlugin(array('param_name'=>'image'))->post();
        $errors = array();
        if(is_array($return)) {
            if(!empty($return)) {
                foreach($return as $fileInfo) {
                    if(!empty($fileInfo->error)) {
                        $errors[] = trim($fileInfo->error);
                    }
                }
            }
        } else {
            if(!empty($return->error)) {
                $errors[] = trim($return->error);
            }
        }
        if(!empty($errors)) {
            $this->answer = array(
                "errorMessage" => trim(implode("\n", $errors))
            );
        } else {
            $this->answer = $return;
        }
        return $this->AnswerPlugin()->format($this->answer, !empty($errors) ? 400 : 0);
    }

    public function generatePdfAction() {
        if (($checkListId = (int)$this->params('id')) !== null) {

            $checkList = null;

            $query = $this->em->createQuery("SELECT cl FROM SafeStartApi\Entity\CheckList cl WHERE cl.id = :id OR cl.hash = :hash");
            $query->setParameters(array('id' => $checkListId, 'hash' => $checkListId));
            $queryResult = $query->getResult();
            if(is_array($queryResult) && !empty($queryResult)) {
                if(isset($queryResult[0])) {
                    $checkList = $queryResult[0];
                }
            }

            if ($checkList !== null) {
                $this->PdfPlugin($checkList->getId());
                return;
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