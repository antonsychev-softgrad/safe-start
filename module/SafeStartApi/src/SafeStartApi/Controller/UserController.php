<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\Exception\Rest403;
use SafeStartApi\Base\RestController;
use Zend\Authentication\Result;

class UserController extends RestController
{

    const USER_RECOVERY_INTERVAL_HOURS = 3;
    const REMEMBER_ME_SECONDS = 31536000; // 1 year

    public function loginAction()
    {
        if (!$this->_requestIsValid('user/login'))
            return $this->_showBadRequest();

        if ($this->authService->hasIdentity()) {
            $userInfo = $this->authService->getStorage()->read();
            if ($userInfo->getDeleted())
                return $this->_showUserUnavailable('User has been removed');
            if (!$userInfo->getEnabled())
                return $this->_showUserUnavailable("User's account is unavailable");
            if ($this->_checkExpiryDate())
                throw new Rest403('Your company subscription expired');
            if (!$this->_checkIfCompanyExists())
                throw new Rest403('Your company has been blocked');
            $errorCode = RestController::USER_ALREADY_LOGGED_IN_ERROR;

            $this->answer = array(
                'authToken'    => $this->sessionManager->getId(),
                'userInfo'     => $userInfo->toArray(),
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

        $adapterOptions                     = $this->moduleConfig['doctrine']['authentication']['orm_default'];
        $adapterOptions['object_manager']   = $this->getServiceLocator()->get($adapterOptions['object_manager']);
        $adapterOptions['identityProperty'] = $identityProperty;

        $adapter->setOptions($adapterOptions);

        $adapter->setIdentityValue($identity);
        $adapter->setCredentialValue($password);
        $result = $this->authService->authenticate();

        $userRep = $this->em->getRepository('SafeStartApi\Entity\User');

        $authCode  = $result->getCode();
        $userInfo  = '';
        $errorCode = 0;

        switch ($authCode) {
            case Result::SUCCESS:
                $errorMessage = '';
                $user         = $userRep->findOneBy(array($identityProperty => $identity));
                if ($user) {
                    if ($user->getDeleted()) {
                        $this->authService->clearIdentity();

                        return $this->_showUserUnavailable('User has been removed');
                    }
                    if (!$user->getEnabled()) {
                        $this->authService->clearIdentity();

                        return $this->_showUserUnavailable("User's account is unavailable");
                    }
                    if ($this->_checkExpiryDate()) {
                        $this->authService->clearIdentity();
                        throw new Rest403('Your company subscription expired');
                    }

                    if (!$this->_checkIfCompanyExists()) {
                        $this->authService->clearIdentity();
                        throw new Rest403('Your company has been blocked');
                    }

                    $user->setLastLogin(new \DateTime());
                    if (isset($this->data->device))
                        $user->setDevice(strtolower($this->data->device));
                    if (isset($this->data->deviceId) && $this->data->deviceId !== '') {
                        $user->setDeviceId($this->data->deviceId);
                    }
                    $this->em->flush();

                    if(isset($this->data->remember) && $this->data->remember) {
                        $this->sessionManager->rememberMe(self::REMEMBER_ME_SECONDS);
                    } else {
                        $this->sessionManager->forgetMe();
                    }

                    $userInfo       = $user->toArray();
                    $userData       = new \stdClass();
                    $userData->user = $userInfo;
                    $this->authService->getStorage()->write($user);
                    $this->authToken = $this->sessionManager->getId();
                } else {
                    $errorMessage = 'Identity not found';
                    $errorCode    = RestController::USER_NOT_FOUND_ERROR;
                }
                break;
            case Result::FAILURE_IDENTITY_NOT_FOUND:
                $errorMessage = 'Identity not found';
                $errorCode    = RestController::USER_NOT_FOUND_ERROR;
                break;
            case Result::FAILURE_CREDENTIAL_INVALID:
                $errorMessage = 'Invalid credential';
                $errorCode    = RestController::INVALID_CREDENTIAL_ERROR;
                break;
            default:
                $errorMessage = 'Error authorisation';
                break;
        }

        $this->answer = array();

        if ($userInfo)
            $this->answer['userInfo'] = $userInfo;
        if ($this->authToken)
            $this->answer['authToken'] = $this->authToken;
        if ($errorMessage)
            $this->answer['errorMessage'] = $errorMessage;

        return $this->AnswerPlugin()->format($this->answer, $errorCode);
    }

    public function logoutAction()
    {
        $this->cleatRequestLimits();

        $this->authService->clearIdentity();
        $this->sessionManager->destroy(array('clear_storage' => true));

        $this->answer = array(
            'done' => true
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function updateAction()
    {
        if (!$this->authService->hasIdentity())
            throw new Rest401('Access denied');

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
            if (isset($this->data->email) && $this->data->email != $user->getEmail()) {
                $checkUser = $this->em->getRepository('SafeStartApi\Entity\User')->findOneBy(array(
                    'email' => $this->data->email
                ));
                if (!is_null($checkUser))
                    return $this->_showUserAlreadyInUse();
            }
            if (isset($this->data->username) && $this->data->username != $user->getUsername()) {
                $checkUser = $this->em->getRepository('SafeStartApi\Entity\User')->findOneBy(array(
                    'username' => $this->data->username
                ));
                if (!is_null($checkUser))
                    return $this->_showUserAlreadyInUse();
            }
        } else {
            $user = new \SafeStartApi\Entity\User();
            if (!isset($this->data->email))
                $this->data->email = uniqid() . '@safestartinspections.com';
            if (isset($this->data->email)) {
                $checkUser = $this->em->getRepository('SafeStartApi\Entity\User')->findOneBy(array(
                    'email' => $this->data->email
                ));

                if (!is_null($checkUser))
                    return $this->_showUserAlreadyInUse();
            }
            if (isset($this->data->username)) {
                $checkUser = $this->em->getRepository('SafeStartApi\Entity\User')->findOneBy(array(
                    'username' => $this->data->username
                ));

                if (!is_null($checkUser))
                    return $this->_showUserAlreadyInUse();
            }
            $user->setDeleted(0);
        }

        if (isset($this->data->email))
            $user->setEmail($this->data->email);
        $user->setUsername(isset($this->data->username) ? $this->data->username : $this->data->email);
        $user->setFirstName($this->data->firstName);
        $user->setLastName($this->data->lastName);
        $user->setPosition($this->data->position);
        $user->setDepartment($this->data->department);

        if (isset($this->data->role)) {
            if (!in_array($this->data->role, array(
                'companyUser',
                'companyManager'
            ))
            ) {
                $this->answer = array(
                    "errorMessage" => "Wrong user role"
                );

                return $this->AnswerPlugin()->format($this->answer, 401);
            }
            $user->setRole($this->data->role);
        }
        if ($this->data->enabled)
            $user->setEnabled((bool)$this->data->enabled);

        $this->em->persist($user);

        if (isset($this->data->companyId)) {
            $company = $this->em->find('SafeStartApi\Entity\Company', $this->data->companyId);
            if (!$company) {
                $this->answer = array(
                    "errorMessage" => "Company not found."
                );

                return $this->AnswerPlugin()->format($this->answer, 404);
            }

            // may need replace 1 on 2
            if (!$userId && $company->getRestricted() && !$company->getUnlimUsers() && ((count($company->getUsers()) + 1) > $company->getMaxUsers()))
                return $this->_showCompanyLimitReached('Company limit of users reached');
            $user->setCompany($company);
        }

        if (!$user->getId()) {
            $companyVehicles = $user->getCompany()->getVehicles();
            if ($companyVehicles) {
                foreach ($companyVehicles as $companyVehicle) {
                    $companyVehicle->addUser($user);
                }
            }
        }

        $this->em->flush();

        $this->answer = array(
            'done'   => true,
            'userId' => $user->getId(),
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function deleteAction()
    {
        if (!$this->authService->hasIdentity())
            throw new Rest401('Access denied');

        // todo: check access to user deleting

        $userId = (int)$this->params('id');

        $user = $this->em->find('SafeStartApi\Entity\User', $userId);
        if (!$user) {
            $this->answer = array(
                "errorMessage" => "User not found."
            );

            return $this->AnswerPlugin()->format($this->answer, 404);
        }

        $user->setUsername(time() . " " . $user->getUsername());
        $user->setEmail(time() . " " . $user->getEmail());

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

        $this->MailPlugin()->send('Credentials', $user->getEmail(), 'creds.phtml', array(
                'username'              => $user->getEmail(),
                'firstName'             => $user->getFirstName(),
                'password'              => $password,
                'siteUrl'               => $config['params']['site_url'],
                'emailStaticContentUrl' => $config['params']['email_static_content_url']
            ));

        $this->answer = array(
            'done' => true
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function forgotPasswordAction()
    {
        // $userId = (int)$this->params('id');
        $email = $this->data->email;

        $user = $this->em->getRepository('SafeStartApi\Entity\User')->findOneBy(array('email' => $email));
        if (!$user) {
            $this->answer = array(
                "errorMessage" => "User not found."
            );

            return $this->AnswerPlugin()->format($this->answer, 404);
        }

        $token = md5($user->getId() . time() . rand());
        $now   = new \DateTime();
        $now->add(new \DateInterval('PT' . self::USER_RECOVERY_INTERVAL_HOURS . 'H'));

        $user->setRecoveryToken($token);
        $user->setRecoveryExpire($now);
        $this->em->flush();

        $config = $this->getServiceLocator()->get('Config');

        $this->MailPlugin()->send('Recovery Password', $user->getEmail(), 'recovery.phtml', array(
                'username'              => $user->getEmail(),
                'firstName'             => $user->getFirstName(),
                'recoveryToken'         => $token,
                'siteUrl'               => $config['params']['site_url'],
                'emailStaticContentUrl' => $config['params']['email_static_content_url']
            ));

        $this->answer = array(
            'done' => true
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function resetPasswordAction()
    {
        $token = $this->params('token');
        $now   = new \DateTime();

        if (strlen($token) !== 32) {
            $this->answer = array(
                "msg" => "Invalid Link"
            );

            return $this->AnswerPlugin()->format($this->answer, 0);
        }

        $query = $this->em->createQuery("select u from SafeStartApi\Entity\User u where u.recoveryToken = ?1 and u.recoveryExpire > ?2");
        $query->setParameter(1, $token);
        $query->setParameter(2, $now);
        $users = $query->getResult();

        if (!isset($users[0])) {
            $this->answer = array(
                "msg" => "Link Outdated"
            );

            return $this->AnswerPlugin()->format($this->answer, 0);
        }
        $user = $users[0];

        $user->setRecoveryExpire($now);
        $password = substr(md5($user->getId() . time() . rand()), 0, 6);
        $user->setPlainPassword($password);
        $this->em->flush();

        $config = $this->getServiceLocator()->get('Config');

        $this->MailPlugin()->send('New password', $user->getEmail(), 'forgotpassword.phtml', array(
                'username'              => $user->getEmail(),
                'firstName'             => $user->getFirstName(),
                'password'              => $password,
                'siteUrl'               => $config['params']['site_url'],
                'emailStaticContentUrl' => $config['params']['email_static_content_url']
            ));

        $this->answer = array(
            "msg" => "New password was sent to your email"
        );

        return $this->AnswerPlugin()->format($this->answer, 0);
    }

    public function syncAction()
    {
        $this->UserSyncPlugin()->syncUsers();
    }
}
