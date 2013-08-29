<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestController;

class WebPanelController extends RestController
{
    public function getMainMenuAction()
    {
        $mainMenu = array();
        $userInfo = array();

        if (!$this->authService->hasIdentity()) {
            $mainMenu[] = 'Auth';
        } else {
            $user = $this->authService->getStorage()->read();
            if ($user) $userInfo = $user->toArray();
        }

        if($this->AclPlugin()->isAllowed('adminPanel', 'viewCompaniesPage')) {
            $mainMenu[] = 'Companies';
            $mainMenu[] = 'Company';
            $mainMenu[] = 'Users';
        }

        if($this->AclPlugin()->isAllowed('userPanel', 'view')) {
            $mainMenu[] = 'Checklist';
        }

        $mainMenu[] = 'Contact';

        $this->answer = array(
            'mainMenu' => $mainMenu,
            'userInfo' => $userInfo
        );

        return $this->AnswerPlugin()->format($this->answer);
    }
}
