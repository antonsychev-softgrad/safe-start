<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestController;

class IndexController extends RestController
{
    public function indexAction()
    {
       return $this->pingAction();
    }

    public function pingAction()
    {
        $this->answer = array(
            'done' => true,
            'version' => $this->moduleConfig['params']['version'],
        );

        $this->viewModel->setVariable('answer', $this->answer);
        return $this->viewModel;
    }
}
