<?php

namespace SafeStartApi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DocsController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }

}
