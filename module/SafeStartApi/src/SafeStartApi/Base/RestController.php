<?php

namespace SafeStartApi\Base;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;
use Zend\Session\SessionManager;

class RestController extends AbstractActionController
{
    protected $moduleConfig;

    protected $answer;

    public function __construct()
    {
        $container = new Container('SafeStartAppUser');

        $authToken = $this->params()->fromHeader('X-Auth-Token');
        if (!empty($authToken)) {
            $container->setId($authToken);
        }

        $this->getEventManager()->attach('dispatch', array($this, 'onDispatchEvent'), 100);
    }

    public function onDispatchEvent()
    {
        $this->moduleConfig = $this->getServiceLocator()->get('Config');
    }

}
