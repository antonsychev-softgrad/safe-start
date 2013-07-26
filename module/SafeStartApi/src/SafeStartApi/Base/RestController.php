<?php

namespace SafeStartApi\Base;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\EventManager\EventManagerInterface;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;

class RestController extends AbstractActionController
{

    protected $viewModel;

    protected $answer;

    public function onDispatch(MvcEvent $mvcEvent)
    {
        $this->viewModel = new ViewModel;
        $this->viewModel->setTemplate('ajax/200');
        $this->viewModel->setTerminal(true);

        return parent::onDispatch($mvcEvent);
    }

    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);

        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            $request = $e->getRequest();
            $method  = $request->getMethod();
        }, 100); // execute before executing action logic
    }

}
