<?php

namespace SafeStartApi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\PhpEnvironment\Request;
use Zend\View\Model\ViewModel;
use SafeStartApi\Base\RestController;

class InfoController extends RestController
{
    public function getImageAction()
    {
        $hash = $this->params('hash');
        $size = $this->params('size');
        if (empty($hash)) return false;
        //todo: почекать все расширения изображений (jpg, png); есои полный размер есть а $size нету нужно его создать
        if (file_exists($this->getFileSystemPath('data/users/') . $hash . $size . '.jpg')) {
            $fileSizeInfo = @getimagesize($this->getFileSystemPath('data/users/') . $hash . $size . '.jpg');
            if ($fileSizeInfo) { // it`s image
                header('Content-Type: ' . $fileSizeInfo['mime']);
                header('Content-Length: ' . filesize($this->getFileSystemPath('data/users/') . $hash . $size . '.jpg'));
                echo file_get_contents($this->getFileSystemPath('data/users/') . $hash . $size . '.jpg');
            }
        }
        return false;
    }

    public function getDefaultChecklistAction()
    {
        $query = $this->em->createQuery('SELECT f FROM SafeStartApi\Entity\DefaultField f WHERE f.deleted = 0 AND f.enabled = 1');
        $items = $query->getResult();
        $checklist = $this->GetDataPlugin()->buildChecklist($items);
        $this->answer = array(
            'checklist' => $checklist,
        );
        return $this->AnswerPlugin()->format($this->answer);
    }

    protected function getFileSystemPath($fEndPath = null)
    {
        $root = $_SERVER['DOCUMENT_ROOT'];
        if (!file_exists($root . "/init_autoloader.php")) {
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
        } else {
            $fEndPath = preg_replace('/^(.*)$/isU', "$1", $fEndPath);
        }

        $returnFolder = $root . '/' . $fEndPath;
        if (!preg_match('/.*(\/)$/isU', $returnFolder, $match)) {
            $returnFolder .= '/';
        }

        return $returnFolder;
    }
}