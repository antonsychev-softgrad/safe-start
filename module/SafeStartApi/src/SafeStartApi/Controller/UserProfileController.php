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
        $user = $this->authService->getStorage()->read();
        $userInfo = $user->toArray();
        if ((int)$userId != $userInfo['id']) return $this->AnswerPlugin()->format(array('errorMessage' => 'Acess denied'), 403, 200);
        if (!$this->_requestIsValid('userprofile/update')) return $this->_showBadRequest();
        $currentEmail = $user->getEmail();
        if($currentEmail != $this->data->email) {
            if($this->ValidationPlugin()->isEmailExists($this->data->email)) return $this->_showEmailExists();
            if(!$this->ValidationPlugin()->isValidEmail($this->data->email)) return $this->_showEmailInvalid();
        }

        $newPassword = isset($this->data->newPassword) ? $this->data->newPassword : false;
        $confirmPassword = isset($this->data->confirmPassword) ? $this->data->confirmPassword : false;

        $user = $this->em->find('SafeStartApi\Entity\User', $userId);
        if($newPassword && $confirmPassword && $newPassword != $confirmPassword) {
            return $this->_showPassIsNotEqual();
        } elseif($newPassword && $confirmPassword && $newPassword == $confirmPassword) {
            $user->setPlainPassword($newPassword);
        }
        $user->setFirstName($this->data->firstName);
        $user->setLastName($this->data->lastName);
        $user->setEmail($this->data->email);
        $this->em->flush();

        $this->authService->getStorage()->write($user);

        $this->answer = array(
            'done' => true,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function updateSignatureAction()
    {
        $user = $this->authService->getStorage()->read();
        if (!$this->_requestIsValid('userprofile/updatesignature')) return $this->_showBadRequest();

        $user->setSignature($this->data->signature);
        $this->em->flush();

        $this->authService->getStorage()->write($user);

        $this->answer = array(
            'done' => true,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }
}
