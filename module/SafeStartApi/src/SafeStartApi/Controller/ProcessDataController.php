<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\PublicAccessRestController;

class ProcessDataController extends PublicAccessRestController
{
    public function uploadImagesAction()
    {
        $return = $this->UploadPlugin(array('param_name' => 'image'))->post();
        $errors = array();
        if (is_array($return)) {
            if (!empty($return)) {
                foreach ($return as $fileInfo) {
                    if (!empty($fileInfo->error)) {
                        $errors[] = trim($fileInfo->error);
                    }
                }
            }
        } else {
            if (!empty($return->error)) {
                $errors[] = trim($return->error);
            }
        }
        if (!empty($errors)) {
            $this->answer = array(
                "errorMessage" => trim(implode("\n", $errors))
            );
        } else {
            $return = (array) $return;
            $fileHash = isset($return['hash']) ? $return['hash'] : null;
            $defUsersPath = $this->moduleConfig['defUsersPath'];
            $searchDir = \SafeStartApi\Application::getFileSystemPath($defUsersPath);
            $filePath = $this->getFileByDirAndName($searchDir, $fileHash);
            if (file_exists($filePath)) chmod($filePath, 0777);
            $this->answer = $return;
        }
        return $this->AnswerPlugin()->format($this->answer, !empty($errors) ? 400 : 0);
    }

    protected function getFileByDirAndName($dir, $tosearch) {
        if(file_exists($dir) && is_dir($dir)) {

            $validFileExts = array(
                "jpg", "jpeg", "png"
            );

            $path = $dir.$tosearch;
            $ext = preg_replace('/.*\.([^\.]*)$/is','$1', $tosearch);
            if(file_exists($path) && is_file($path) && ($ext != $tosearch)) {
                return (realpath($path));
            } else {
                foreach($validFileExts as $validExt) {
                    $filename = $path . "." . $validExt;
                    if(file_exists($filename) && !is_dir($filename)) {
                        return (realpath($filename));
                    }
                }
            }
        }
        return false;
    }

    public function generatePdfAction()
    {
        $checkListId = (string)$this->params('id');

        $qb = $this->em->createQueryBuilder();
        $qb->select('e')
            ->from('SafeStartApi\Entity\CheckList', 'e')
            ->where(
                $qb->expr()->eq('e.id', ":id")
            )
            ->orWhere(
                $qb->expr()->eq('e.hash', ":hash")
            )
            ->setParameters(array(
                'id' => $checkListId,
                'hash' => $checkListId));
        $checkList = $qb->getQuery()->getOneOrNullResult();
        if (!$checkList)
            return $this->getController()->_showNotFound('Requested inspection not found.');

        $link = $checkList->getPdfLink();
        $path = '';//$this->inspectionPdf()->getFilePathByName($link);
        if (!$link || !file_exists($path))
            $path = $this->inspectionPdf()->create($checkList);

        header("Content-Disposition: inline; filename={$checkList->getPdfLink()}");
        header("Content-type: application/x-pdf");
        echo file_get_contents($path);
        return true;
    }
}