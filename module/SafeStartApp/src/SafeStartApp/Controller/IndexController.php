<?php

namespace SafeStartApp\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $config = $this->getServiceLocator()->get('Config');
        $authService = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
        if ($authService->hasIdentity()) {
            $user = $authService->getStorage()->read();
            if ($user) {
                $config['safe-start-app']['currentUser'] = $user->toArray();
            }
        }
        $this->layout()->setVariable('appConfig', $config['safe-start-app']);
        return new ViewModel();
    }
}
