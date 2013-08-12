<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestrictedAccessRestController;

/**
 * Class UserProfileController
 * @package SafeStartApi\Controller
 */
class UserProfileController extends RestrictedAccessRestController
{
    public function updateAction()
    {
        $userId = (int)$this->params('id');
        $userInfo = $this->authService->getStorage()->read()->toArray();
        if ((int)$userId != $userInfo['id']) return $this->AnswerPlugin()->format(array('errorMessage' => 'Acess denied'), 403, 403);
        if (!$this->_requestIsValid('userprofile/update')) return $this->_showBadRequest();

        $user = $this->em->find('SafeStartApi\Entity\User', $userId);
        $user->setFirstName($this->data->firstName);
        $user->setLastName($this->data->lastName);
        $user->setEmail($this->data->email);
        $this->em->flush();

        $this->answer = array(
            'done' => true,
        );

        return $this->AnswerPlugin()->format($this->answer);

    }
}
