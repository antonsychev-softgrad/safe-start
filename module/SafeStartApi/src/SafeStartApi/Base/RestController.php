<?php

namespace SafeStartApi\Base;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class RestController extends AbstractActionController
{

    protected $viewModel;

    protected $answer;

    public function __construct() {
        $this->getEventManager()->attach('dispatch', array($this, 'onDispatchEvent'), 100);
    }

    public function onDispatchEvent($e) {
        $this->viewModel = new ViewModel;
        $this->viewModel->setTemplate('ajax/200');
        $this->viewModel->setTerminal(true);
    }

}
