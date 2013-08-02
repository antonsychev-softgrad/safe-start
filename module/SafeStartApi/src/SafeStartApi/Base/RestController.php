<?php

namespace SafeStartApi\Base;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;
use Zend\Session\SessionManager;
use Zend\Session\Container as SessionContainer;

class RestController extends AbstractActionController
{
    protected $moduleConfig;

    protected $answer;
    protected $meta;
    protected $data;
    protected $headers;

    public $sessionManager;
    public $authService;
    public $authToken = null;

    public function __construct()
    {
        $this->getEventManager()->attach('dispatch', array($this, 'onDispatchEvent'), 100);

    }

    public function onDispatchEvent()
    {

        $this->moduleConfig = $this->getServiceLocator()->get('Config');

        $this->headers = $this->params()->fromHeader();
        // TODO: parse data and meta;
        $this->data = $this->params()->fromPost();

        $this->sessionManager = $this->getServiceLocator()->get('Zend\Session\SessionManager');
        $this->authService = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');

        // if session not started and X-Auth-Token set need restart session by id
        $this->authToken = isset($this->headers['X-Auth-Token']) ? $this->headers['X-Auth-Token'] : null;
        if (!empty($authToken) && !$this->authService->hasIdentity()) {
            $this->sessionManager->setId($this->authToken);
            $this->sessionManager->start();
        }
    }

}
