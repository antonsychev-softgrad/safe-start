<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestController;

class UserController extends RestController
{
    public function loginAction()
    {
        $this->answer = array(
            'session_id' => 0,
            'authorised' => 0,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function logoutAction()
    {
        $this->answer = array(
            'authorised' => 0,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }
}
