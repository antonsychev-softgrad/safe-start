<?php

namespace SafeStartApi\Base;

use Zend\Mvc\Controller\AbstractActionController;

class RestController extends AbstractActionController
{
    protected $moduleConfig;

    protected $answer;

    public function __construct()
    {
        $this->getEventManager()->attach('dispatch', array($this, 'onDispatchEvent'), 100);
    }

    public function onDispatchEvent()
    {
        $this->moduleConfig = $this->getServiceLocator()->get('Config');
    }

}
