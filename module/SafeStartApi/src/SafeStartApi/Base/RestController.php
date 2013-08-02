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
    protected $meta;
    protected $data;
    protected $headers;

    public function __construct()
    {
        $headers = $this->params()->fromHeader();

        $authToken = isset($headers['X-Auth-Token']) ? $headers['X-Auth-Token'] : '';

        $session = new SessionManager();
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
