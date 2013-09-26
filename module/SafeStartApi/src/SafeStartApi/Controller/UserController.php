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

        //todo: check if user enabled; checkcompany epiry date

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

        $identity = isset($this->data->username) ? $this->data->username : '';
        $password = isset($this->data->password) ? $this->data->password : '';

        $adapter = $this->authService->getAdapter();

        if ($this->ValidationPlugin()->isValidEmail($identity)) {
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
                if ($user) {
                    $userInfo = $user->toArray();
                    $user->setLastLogin(new \DateTime());
                    if (isset($this->data->device)) $user->setDevice(strtolower($this->data->device));
                    if (isset($this->data->deviceId)) $user->setDeviceId($this->data->deviceId);
                    $this->em->flush();
                    $userData = new \stdClass();
                    $userData->user = $userInfo;
                    $this->authService->getStorage()->write($user);
                    $this->authToken = $this->sessionManager->getId();
                } else {
                    $errorMessage = 'Identity not found';
                    $errorCode = RestController::USER_NOT_FOUND_ERROR;
                }
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
                $errorMessage = 'Error authorisation';
                break;
        }

        $this->answer = array();

        if ($userInfo) $this->answer['userInfo'] = $userInfo;
        if ($this->authToken) $this->answer['authToken'] = $this->authToken;
        if ($errorMessage) $this->answer['errorMessage'] = $errorMessage;

        return $this->AnswerPlugin()->format($this->answer, $errorCode);
    }

    public function logoutAction()
    {
        $this->cleatRequestLimits();
        $this->answer = array(
            'done' => $this->authService->clearIdentity(),
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function updateAction()
    {
        if (!$this->authService->hasIdentity()) throw new Rest401('Access denied');

        // todo: check access to user updating

        $userId = (int)$this->params('id');

        if ($userId) {
            $user = $this->em->find('SafeStartApi\Entity\User', $userId);
            if (!$user) {
                $this->answer = array(
                    "errorMessage" => "User not found."
                );
                return $this->AnswerPlugin()->format($this->answer, 404);
            }
        } else {
            $user = new \SafeStartApi\Entity\User();
        }

        // todo: check if unique email if user deleted = 1 and username

        if (isset($this->data->email)) $user->setEmail($this->data->email);
        $user->setUsername(isset($this->data->username) ? $this->data->username : $this->data->email);
        $user->setDeleted(0);
        $user->setFirstName($this->data->firstName);
        $user->setLastName($this->data->lastName);
        $user->setPosition($this->data->position);
        $user->setDepartment($this->data->department);

        if (isset($this->data->role)) {
            if (!in_array($this->data->role, array('companyUser', 'companyManager'))) {
                $this->answer = array(
                    "errorMessage" => "Wrong user role"
                );
                return $this->AnswerPlugin()->format($this->answer, 401);
            }
            $user->setRole($this->data->role);
        }
        if ($this->data->enabled) $user->setEnabled((bool)$this->data->enabled);

        $this->em->persist($user);


        if (isset($this->data->companyId)) {
            $company = $this->em->find('SafeStartApi\Entity\Company', $this->data->companyId);
            if (!$company) {
                $this->answer = array(
                    "errorMessage" => "Company not found."
                );
                return $this->AnswerPlugin()->format($this->answer, 404);
            }

            if (!$userId && $company->getRestricted() && ((count($company->getVehicles()) + 1) > $company->getMaxVehicles())) return $this->_showCompanyLimitReached('Company limit of users reached');
            $user->setCompany($company);
        }

        $this->em->flush();

        $this->answer = array(
            'done' => true,
            'userId' => $user->getId(),
        );

        return $this->AnswerPlugin()->format($this->answer);

    }

    public function deleteAction()
    {
        if (!$this->authService->hasIdentity()) throw new Rest401('Access denied');

        // todo: check access to user deleting

        $userId = (int)$this->params('id');

        $user = $this->em->find('SafeStartApi\Entity\User', $userId);
        if (!$user) {
            $this->answer = array(
                "errorMessage" => "User not found."
            );
            return $this->AnswerPlugin()->format($this->answer, 404);
        }

        $user->setDeleted(1);
        $this->em->flush();

        $this->answer = array(
            'done' => true
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function sendCredentialsAction()
    {
        //   if (!$this->_requestIsValid('admin/sendcredentials')) return $this->_showBadRequest();

        $userId = (int)$this->params('id');

        $user = $this->em->find('SafeStartApi\Entity\User', $userId);
        if (!$user) {
            $this->answer = array(
                "errorMessage" => "User not found."
            );
            return $this->AnswerPlugin()->format($this->answer, 404);
        }

        $password = substr(md5($user->getId() . time() . rand()), 0, 6);
        $user->setPlainPassword($password);
        $this->em->flush();

        $config = $this->getServiceLocator()->get('Config');

        $this->MailPlugin()->send(
            'Credentials',
            $user->getEmail(),
            'creds.phtml',
            array(
                'username' => $user->getUsername(),
                'firstName' => $user->getFirstName(),
                'password' => $password,
                'siteUrl' => $config['safe-start-app']['siteUrl']
            )
        );

        $this->answer = array(
            'done' => true
        );

        return $this->AnswerPlugin()->format($this->answer);
    }
}
