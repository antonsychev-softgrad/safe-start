<?php

namespace SafeStartApi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\PhpEnvironment\Request;
use Zend\View\Model\ViewModel;
use SafeStartApi\Base\RestController;

class InfoController extends RestController
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

    protected function get_user_id() {
        $user_folder = '';
        try {
            if (isset($this->authService)) {
                if ($this->authService->hasIdentity()) {
                    $user = $this->authService->getStorage()->read();
                    $user_folder = "".$user->getId()."/";
                }
            }
        }
        catch (\Exception $e) {

        }
        return $user_folder;
    }

    protected function get_full_path($fEndPath = null) {
        $root = $_SERVER['DOCUMENT_ROOT'];
        if(!file_exists($root . "/init_autoloader.php")) {
            $root = dirname($root);
        }

        if ($fEndPath === null || !is_string($fEndPath)) {
            $moduleConfig = $this->getServiceLocator()->get('Config');
            $fEndPath = isset($moduleConfig['defUsersPath']) ? $moduleConfig['defUsersPath'] : '/';
        }

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

        $returnFolder .= $this->get_user_id();

        return $returnFolder;
    }

    public function getImageAction() {

        if(($image = $this->params('image')) === null) {
            $request      = $this->getRequest();
            $image  = $request->getQuery('image');
        }

        if(($image !== null)) {
            $filePath = $this->get_full_path();
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