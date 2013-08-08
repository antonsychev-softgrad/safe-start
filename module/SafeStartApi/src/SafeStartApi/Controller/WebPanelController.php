<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestController;

class WebPanelController extends RestController
{
    public function getMainMenuAction()
    {
        $mainMenu = array();

        if (!$this->authService->hasIdentity()) {
            $mainMenu[] = 'Auth';
            $mainMenu[] = 'Contact';
        }

        if($this->AclPlugin()->isAllowed('adminPanel', 'viewCompaniesPage')) $mainMenu[] = 'Companies';

        $this->answer = array(
            'mainMenu' => $mainMenu
        );

        return $this->AnswerPlugin()->format($this->answer);
    }
}
