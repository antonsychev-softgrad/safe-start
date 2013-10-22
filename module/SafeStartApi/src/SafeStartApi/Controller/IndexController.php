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
            'version' => $this->moduleConfig['params']['version'],
        );
        $this->queues('test');
        return $this->AnswerPlugin()->format($this->answer);
    }
}
