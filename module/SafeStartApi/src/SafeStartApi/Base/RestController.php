<?php

namespace SafeStartApi\Base;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;

class RestController extends AbstractActionController
{
    protected $moduleConfig;

    protected $answer;

    public function __construct()
    {

        $authToken = $this->params()->fromHeader('X-Auth-Token');
        $session = $this->getEventManager()
            ->getApplication()
            ->getServiceManager()
            ->get('Zend\Session\SessionManager');
        if (!empty($authToken)) {
            $session->setId($authToken);
        } else {
            $authToken = substr(md5(time() . rand()), 0, 12);
            $session->setId($authToken);
        }

        $this->getEventManager()->attach('dispatch', array($this, 'onDispatchEvent'), 100);
    }

    public function onDispatchEvent()
    {
        $this->moduleConfig = $this->getServiceLocator()->get('Config');
    }

}
