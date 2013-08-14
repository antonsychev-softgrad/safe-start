<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestController;
use Zend\Authentication\Result;
use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;

class UserController extends RestController
{

    public function loginAction()
    {
        if (!$this->_requestIsValid('user/login')) return $this->_showBadRequest();

        if ($this->authService->hasIdentity()) {
            $userInfo = $this->authService->getStorage()->read();
            $errorCode = RestController::USER_ALREADY_LOGGED_IN_ERROR;
            $this->answer = array(
                'authToken' => $this->sessionManager->getId(),
                'userInfo' => $userInfo->toArray(),
                'errorMessage' => 'User already logged in',
            );
            return $this->AnswerPlugin()->format($this->answer, $errorCode);
        }

        $identity = isset($this->data->identity) ? $this->data->identity : '';
        $password = isset($this->data->password) ? $this->data->password : '';

        $adapter = $this->authService->getAdapter();

        if ($this->validationPlugin()->isValidEmail($identity)) {
            $identityProperty = 'email';
        } else {
            $identityProperty = 'username';
        }

        $adapterOptions = $this->moduleConfig['doctrine']['authentication']['orm_default'];
        $adapterOptions['object_manager'] = $this->getServiceLocator()->get($adapterOptions['object_manager']);
        $adapterOptions['identityProperty'] = $identityProperty;

        $adapter->setOptions($adapterOptions);

        $adapter->setIdentityValue($identity);
        $adapter->setCredentialValue($password);
        $result = $this->authService->authenticate();

        $userRep = $this->em->getRepository('SafeStartApi\Entity\User');

        $authCode = $result->getCode();
        $userInfo = '';
        $errorCode = 0;

        switch ($authCode) {
            case Result::SUCCESS:
                $errorMessage = '';
                $user = $userRep->findOneBy(array($identityProperty => $identity));
                if($user) {
                    $userInfo = $user->toArray();
                }
                $user->setLastLogin(new \DateTime());
                $this->em->flush();
                $userData = new \stdClass();
                $userData->user = $userInfo;
                $this->authService->getStorage()->write($user);
                $this->authToken = $this->sessionManager->getId();
                break;
            case Result::FAILURE_IDENTITY_NOT_FOUND:
                $errorMessage = 'Identity not found';
                $errorCode = RestController::USER_NOT_FOUND_ERROR;
                break;
            case Result::FAILURE_CREDENTIAL_INVALID:
                $errorMessage = 'Invalid credential';
                $errorCode = RestController::INVALID_CREDENTIAL_ERROR;
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
