<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestController;

class WebPanelController extends RestController
{
    public function indexAction()
    {
        $this->answer = array(
            'mainMenu' => array(
                'Auth'
            ),
        );

        return $this->AnswerPlugin()->format($this->answer);
    }
}
