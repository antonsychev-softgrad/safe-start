<?php

namespace SafeStartApi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\PhpEnvironment\Request;
use Zend\View\Model\ViewModel;
use SafeStartApi\Base\RestController;
use SafeStartApi\Model\ImageProcessor;

class InfoController extends RestController
{
 
    public function getImageAction()
    {
        $hash = $this->params('hash');
        $size = $this->params('size', '');

        if (empty($hash)) return false;

        $moduleConfig = $this->getServiceLocator()->get('Config');
        $defUsersPath = $moduleConfig['defUsersPath'];
        $searchDir = \SafeStartApi\Application::getFileSystemPath($defUsersPath);
        $tosearch = $hash.$size;
        $filePath = \SafeStartApi\Application::getImageFileByDirAndName($searchDir, $tosearch);
        if(!$filePath && !empty($size)) {

            $size = preg_replace("/x/is", "x", $size);

            list($max_width,$max_height) = explode("x", $size);
            $max_width = intval($max_width);
            $max_height = intval($max_height);

            if($max_width > 0 && $max_height > 0) {
                $origFilePath =\SafeStartApi\Application::getImageFileByDirAndName($searchDir, $hash);
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

    public function contactAction()
    {
        $name = $this->data->name;
        $email = $this->data->email;
        $message = $this->data->message;

        if (!$this->ValidationPlugin()->isValidEmail($email)) $this->_showBadRequest();

        $config = $this->getServiceLocator()->get('Config');

        $this->MailPlugin()->send(
            'Message from contact form',
            //$config['params']['emailForContacts'],
            'Anna.Izotova@ocsico.com',
            'contact.phtml',
            array(
                'name' => $name,
                'message' => $message,
                'email' => $email,
                'emailStaticContentUrl' => $config['params']['email_static_content_url']
            )
        );

        $this->answer = array(
            'done' => true
        );

        return $this->AnswerPlugin()->format($this->answer);
    }
}