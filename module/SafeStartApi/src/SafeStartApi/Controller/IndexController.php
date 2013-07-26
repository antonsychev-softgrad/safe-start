<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestController;

class IndexController extends RestController
{
    public function indexAction()
    {
        $this->answer = array(
            'done' => true,
            'version' => '1.0'
        );

        $this->viewModel->setVariable('answer', $this->answer);
        return $this->viewModel;
    }
}
