<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestController;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Session\Container;

class UserController extends RestController
{
    const USER_NOT_FOUND = 101;
    const INVALID_CREDENTIAL = 102;

    public function loginAction()
    {

        $username = $this->params()->fromPost('username', '');
        $password = $this->params()->fromPost('password', '');

        $serviceLocator = $this->getServiceLocator();

        $authService = $serviceLocator->get('doctrine.authenticationservice.orm_default');
        $adapter = $authService->getAdapter();
        $adapter->setIdentityValue($username);
        $adapter->setCredentialValue($password);
        $result = $authService->authenticate();

        $em = $serviceLocator->get('Doctrine\ORM\EntityManager');
        $user_rep = $em->getRepository('SafeStartApi\Entity\User');

        $authCode = $result->getCode();
        $userInfo = '';
        $authToken = '';
        $errorCode = 0;
        switch ($authCode) {
            case Result::SUCCESS:
                $errorMessage = '';
                $user = $user_rep->findOneByUsername('username');
                if($user) {
                    $userInfo = $user->toArray();
                }
                $user->setLastLogin(new \DateTime());
                $em->merge($user);
                $em->flush();

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
            'authToken' => $authToken,
            'userInfo' => $userInfo,
            'errorMessage' => $errorMessage,
        );

        return $this->AnswerPlugin()->format($this->answer, $errorCode);
    }

    public function logoutAction()
    {
        $this->answer = array(
            'authorised' => 0,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }
}
