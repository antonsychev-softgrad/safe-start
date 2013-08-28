<?php

namespace SafeStartApi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\PhpEnvironment\Request;
use Zend\View\Model\ViewModel;

class InfoController extends AbstractActionController
{
    protected static function getFileByDirAndName($dir, $tosearch) {
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

    protected function get_full_path($fEndPath = '/') {
        $root = $_SERVER['DOCUMENT_ROOT'];
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

        return $returnFolder;
    }

    public function getImageAction() {

        $moduleConfig = $this->getServiceLocator()->get('Config');

        $request      = $this->getRequest();
        $userId = (int) $request->getQuery('uid');
        $image  = $request->getQuery('image');

        $userId = (int) $this->params('uid');
        $image  = $this->params('image');

        if(($image !== null)) {
            $filePath = $this->get_full_path($moduleConfig['defUsersPath']);
            if($userId > 0) {
                $filePath .= "{$userId}/";
            }
            $fileName = "{$image}";
            if(($file = self::getFileByDirAndName($filePath, $fileName)) !== null) {
                $fileSizeInfo = @getimagesize($file);
                if($fileSizeInfo) { // it`s image
                    header('Content-Type: ' . $fileSizeInfo['mime']);
                    header('Content-Length: ' . filesize($file));
                    echo file_get_contents($file);
                }
            }
        }

        return false;
    }
}