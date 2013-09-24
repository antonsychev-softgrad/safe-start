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
            'version' => $this->moduleConfig['parsams']['version'],
        );

        return $this->AnswerPlugin()->format($this->answer);
    }
}
