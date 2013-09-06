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
        if (file_exists(\SafeStartApi\Application::getFileSystemPath('data/users/') . $hash . $size . '.jpg')) {
            $fileSizeInfo = @getimagesize(\SafeStartApi\Application::getFileSystemPath('data/users/') . $hash . $size . '.jpg');
            if ($fileSizeInfo) { // it`s image
                header('Content-Type: ' . $fileSizeInfo['mime']);
                header('Content-Length: ' . filesize(\SafeStartApi\Application::getFileSystemPath('data/users/') . $hash . $size . '.jpg'));
                echo file_get_contents(\SafeStartApi\Application::getFileSystemPath('data/users/') . $hash . $size . '.jpg');
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