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
            $mainMenu[] = 'Contact';
        } else {
            $userInfo = $this->authService->getStorage()->read()->toArray();
        }

        if($this->AclPlugin()->isAllowed('adminPanel', 'viewCompaniesPage')) $mainMenu[] = 'Companies';

        $this->answer = array(
            'mainMenu' => $mainMenu,
            'userInfo' => $userInfo
        );

        return $this->AnswerPlugin()->format($this->answer);
    }
}
