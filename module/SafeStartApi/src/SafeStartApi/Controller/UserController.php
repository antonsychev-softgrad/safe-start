<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestController;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Authentication\Storage\Session as SessionStorage;

class UserController extends RestController
{
    public function loginAction()
    {

        $username = '';
        $password = '';

        $serviceLocator = $this->getServiceLocator();

        $authService = $serviceLocator->get('doctrine.authenticationservice.orm_default');
        $authService->setStorage(new SessionStorage('SafeStartAppUser'));
        $adapter = $authService->getAdapter();
        $adapter->setIdentityValue($username);
        $adapter->setCredentialValue($password);
        $result = $authService->authenticate();

        $authCode = $result->getCode();
        switch ($authCode) {
            case Result::FAILURE_IDENTITY_NOT_FOUND:
                $auth_message = 'FAILURE: Identity not found';
                break;
            case Result::FAILURE_CREDENTIAL_INVALID:
                $auth_message = 'FAILURE: Invalid credential';
                break;
            case Result::SUCCESS:
                $auth_message = 'SUCCESS';

                $accessToken = substr(md5($username. time() . rand()), 0, 12);
                SessionStorage::setId($accessToken);

                break;
            default:
                $auth_message = '';
                break;
        }

        $this->answer = array(
            'session_id' => 0,
            'auth_code' => $authCode,
            'auth_message' => $auth_message,
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
