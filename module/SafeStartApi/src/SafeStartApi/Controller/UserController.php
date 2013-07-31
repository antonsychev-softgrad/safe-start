<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestController;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;

class UserController extends RestController
{
    public function loginAction()
    {
        $username = '';
        $password = '';

        $auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
        $adapter = $auth->getAdapter();
        $adapter->setIdentityValue($username);
        $adapter->setCredentialValue($password);
        $result = $auth->authenticate();

        $authCode = $result->getCode();
        switch ($authCode) {
            case Result::FAILURE_IDENTITY_NOT_FOUND:
                $auth_message = 'FAILURE. Identity not found';
                break;
            case Result::FAILURE_CREDENTIAL_INVALID:
                $auth_message = 'FAILURE. Invalid credential';
                break;
            case Result::SUCCESS:
                $auth_message = 'SUCCESS';
                break;
            default:
                $auth_message = '';
                break;
        }

        $this->answer = array(
            'sessionId' => 0,
            'authCode' => $authCode,
            'authMessage' => $auth_message,
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
