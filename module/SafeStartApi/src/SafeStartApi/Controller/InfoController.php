<?php

namespace SafeStartApi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\PhpEnvironment\Request;
use Zend\View\Model\ViewModel;
use SafeStartApi\Base\RestController;
use SafeStartApi\Model\ImageProcessor;

class InfoController extends RestController
{
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


    public function getImageAction()
    {
        $hash = $this->params('hash');
        $size = $this->params('size', '');

        if (empty($hash)) return false;

        $moduleConfig = $this->getServiceLocator()->get('Config');
        $defUsersPath = $moduleConfig['defUsersPath'];
        $searchDir = \SafeStartApi\Application::getFileSystemPath($defUsersPath);
        $tosearch = $hash.$size;
        $filePath = $this->getFileByDirAndName($searchDir, $tosearch);
        if(!$filePath && !empty($size)) {

            $size = preg_replace("/x/is", "x", $size);

            list($max_width,$max_height) = explode("x", $size);
            $max_width = intval($max_width);
            $max_height = intval($max_height);

            if($max_width > 0 && $max_height > 0) {
                $origFilePath = $this->getFileByDirAndName($searchDir, $hash);
                if(!$origFilePath) {
                    return false;
                } else {
                    $version = $max_width . 'x' . $max_height;
                    $newVersionPath = preg_replace('/(\.[^\.]*)$/isU', "{$version}$1", $origFilePath);

                    $imProc = new ImageProcessor($origFilePath);
                    $imProc->contain(array('width' => $max_width, 'height' => $max_height));
                    $imProc->save($newVersionPath);

                    if(file_exists($filePath)) {
                        chmod($filePath,0777);
                    }

                    $filePath = $newVersionPath;
                }
            }
        }

        if(file_exists($filePath)) {
            $fileSizeInfo = @getimagesize($filePath);
            if ($fileSizeInfo) { // it`s image
                header('Content-Type: ' . $fileSizeInfo['mime']);
                header('Content-Length: ' . filesize($filePath));
                echo file_get_contents($filePath);
            }
        }

        return false;
    }

    public function getDefaultChecklistAction()
    {
        $cache = \SafeStartApi\Application::getCache();
        $cashKey = "getDefaultChecklist";

        if ($cache->hasItem($cashKey)) {
            $checklist = $cache->getItem($cashKey);
        } else {
            $query = $this->em->createQuery('SELECT f FROM SafeStartApi\Entity\DefaultField f WHERE f.deleted = 0 AND f.enabled = 1');
            $items = $query->getResult();
            $checklist = $this->GetDataPlugin()->buildChecklist($items);
            $cache->setItem($cashKey, $checklist);
        }

        $this->answer = array(
            'checklist' => $checklist,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }
}