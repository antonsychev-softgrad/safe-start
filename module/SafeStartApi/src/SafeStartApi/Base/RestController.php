<?php

namespace SafeStartApi\Base;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;
use Zend\Session\SessionManager;
//use SafeStartApi\Override\ExtendSessionManager as SessionManager;

class RestController extends AbstractActionController
{
    protected $moduleConfig;

    protected $answer;
    protected $meta;
    protected $data;
    protected $headers;
    protected $authToken;

    public function __construct()
    {
        $this->getEventManager()->attach('dispatch', array($this, 'onDispatchEvent'), 100);
    }

    public function onDispatchEvent()
    {
        $this->moduleConfig = $this->getServiceLocator()->get('Config');

        $this->headers = $this->params()->fromHeader();
        $this->data = $this->params()->fromPost();

        $this->authToken = isset($this->headers['X-Auth-Token']) ? $this->headers['X-Auth-Token'] : '';
        if (empty($authToken)) {
            $this->authToken = substr(md5(time() . rand()), 0, 12);
        }

        $serviceLocator = $this->getServiceLocator();
        $session = $serviceLocator->get('Zend\Session\SessionManager');
        //$session->setId($this->authToken);
        $session->start();

    }

}
