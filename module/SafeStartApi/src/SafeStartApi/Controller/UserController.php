<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestController;
use Zend\Authentication\Result;
use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;

class UserController extends RestController
{
    const USER_NOT_FOUND = 4011;
    const INVALID_CREDENTIAL = 4001;
    const USER_ALREADY_LOGGED_IN = 4002;

    public function loginAction()
    {
        if (!$this->_requestIsValid('user/login')) return $this->_showBadRequest();

        if ($this->authService->hasIdentity()) {
            $userInfo = $this->authService->getStorage()->read();
            $errorCode = static::USER_ALREADY_LOGGED_IN;
            $this->answer = array(
                'authToken' => $this->sessionManager->getId(),
                'userInfo' => $userInfo->toArray(),
                'errorMessage' => 'User already logged in',
            );
            return $this->AnswerPlugin()->format($this->answer, $errorCode);
        }

        $username = isset($this->data->username) ? $this->data->username : '';
        $password = isset($this->data->password) ? $this->data->password : '';

        $adapter = $this->authService->getAdapter();
        $adapter->setIdentityValue($username);
        $adapter->setCredentialValue($password);
        $result = $this->authService->authenticate();

        $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $user_rep = $em->getRepository('SafeStartApi\Entity\User');

        $authCode = $result->getCode();
        $userInfo = '';
        $errorCode = 0;

        switch ($authCode) {
            case Result::SUCCESS:
                $errorMessage = '';
                $user = $user_rep->findOneByUsername($username);
                if($user) {
                    $userInfo = $user->toArray();
                }
                $user->setLastLogin(new \DateTime());
                $em->merge($user);
                $em->flush();
                $userData = new \stdClass();
                $userData->user = $userInfo;
                $this->authService->getStorage()->write($user);
                $this->authToken = $this->sessionManager->getId();
                break;
            case Result::FAILURE_IDENTITY_NOT_FOUND:
                $errorMessage = 'Identity not found';
                $errorCode = self::USER_NOT_FOUND;
                break;
            case Result::FAILURE_CREDENTIAL_INVALID:
                $errorMessage = 'Invalid credential';
                $errorCode = self::INVALID_CREDENTIAL;
                break;
            default:
                $errorMessage = '';
                break;
        }

        $this->answer = array(
            'authToken' => $this->authToken,
            'userInfo' => $userInfo,
            'errorMessage' => $errorMessage,
        );

        return $this->AnswerPlugin()->format($this->answer, $errorCode);
    }

    public function logoutAction()
    {
        $this->answer = array(
            'done' => $this->authService->clearIdentity(),
        );

        return $this->AnswerPlugin()->format($this->answer);
    }
}
