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
        \Resque::enqueue('new_checklist_uploaded', '\SafeStartApi\Jobs\NewDbCheckListUploaded', array(
            'checkListId' => 1
        ));
        return $this->AnswerPlugin()->format($this->answer);
    }
}
