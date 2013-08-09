<?php

namespace SafeStartApp\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $config = $this->getServiceLocator()->get('Config');
        $this->layout()->setVariable('appConfig', $config['safe-start-app']);
        return new ViewModel();
    }
}
