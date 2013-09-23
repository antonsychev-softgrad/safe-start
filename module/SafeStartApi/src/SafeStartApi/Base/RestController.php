<?php

namespace SafeStartApi\Base;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;
use Zend\Session\SessionManager;
use Zend\Session\Container as SessionContainer;
use SafeStartApi\Base\Exception\Rest403;

class RestController extends AbstractActionController
{

    const USER_NOT_FOUND_ERROR = 4011;
    const INVALID_CREDENTIAL_ERROR = 4001;
    const USER_ALREADY_LOGGED_IN_ERROR = 4002;
    const EMAIL_ALREADY_EXISTS_ERROR = 4003;
    const EMAIL_INVALID_ERROR = 40004;
    const NOT_FOUND_ERROR = 4004;
    const REQUESTS_LIMIT_ERROR = 4005;
    const COMPANY_LIMIT_ERROR = 4006;
    const KEY_ALREADY_EXISTS_ERROR = 4007;

    public $moduleConfig;

    protected $answer;
    protected $meta;
    protected $data;
    protected $headers;
    protected $requestJson = '';

    public $sessionManager;
    public $authService;
    public $authToken = null;
    public $em;

    protected $jsonSchemaRetriever;
    protected $jsonSchemaRefResolver;
    public $jsonSchemaValidator;

    public function __construct()
    {
        $this->getEventManager()->attach('dispatch', array($this, 'onDispatchEvent'), 100);

        $this->jsonSchemaRetriever = new \JsonSchema\Uri\UriRetriever();
        $this->jsonSchemaRefResolver = new \JsonSchema\RefResolver;
        $this->jsonSchemaValidator = new \JsonSchema\Validator();
    }

    public function onDispatchEvent()
    {
        $this->_parseRequestFormat();
        $this->_checkAuthToken();
        // todo: find better way for global access
        \SafeStartApi\Application::setCurrentControllerServiceLocator($this->getServiceLocator());
        $this->moduleConfig = $this->getServiceLocator()->get('Config');
        $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        if (!$this->_checkRequestLimits()) throw new Rest403('Requests limit achieved');
    }

    protected function _checkRequestLimits()
    {
        $limitTime = $this->moduleConfig['requestsLimit']['limitTime'];
        if ($this->authService->hasIdentity()) {
            $requestLimits = $this->moduleConfig['requestsLimit']['limitForLoggedInUsers'];
        } else {
            $requestLimits = $this->moduleConfig['requestsLimit']['limitForUnloggedUsers'];
        }

        $cache = \SafeStartApi\Application::getCache();
        $cashKey = $this->_getCashKey();

        if ($cache->hasItem($cashKey)) {
            $statistic = $cache->getItem($cashKey);

            foreach ($statistic as $key => $requestTime) {
                if ($requestTime < time() - $limitTime) {
                    unset($statistic[$key]);
                }
            }
            $requestsCount = count($statistic);

            if ($requestsCount >= $requestLimits) {
                return false;
            }
            if (!$this->authService->hasIdentity()) {
                $statistic[] = time();
                $cache->setItem($cashKey, $statistic);
            }
        } else {
            $statistic = array(time());
            $cache->setItem($cashKey, $statistic);
        }
        return true;
    }

    public function cleatRequestLimits()
    {
        $cache = \SafeStartApi\Application::getCache();
        $cashKey = $this->_getCashKey();
        $cache->removeItem($cashKey);
    }

    protected function _getCashKey()
    {
        $servParam = $this->request->getServer();
        $ip = $servParam->get('REMOTE_ADDR', '');
        $browser = preg_replace('/\s+/', '', $servParam->get('HTTP_USER_AGENT', ''));
        $device = isset($this->data->device) ? $this->data->device : '';
        return $ip . '_' . $browser . '_' . $device;
    }

    protected function _parseRequestFormat()
    {
        $this->requestJson = $this->getRequest()->getContent() ? $this->getRequest()->getContent() : json_encode($this->params()->fromPost());
        $this->headers = $this->getRequest()->getHeaders()->toArray();
        $requestData = json_decode($this->requestJson);
        $this->data = isset($requestData->data) ? $requestData->data : null;
        $this->meta = isset($requestData->meta) ? $requestData->meta : null;
        // set def meta for validation
        if (is_null($this->data) && is_null($this->meta)) {
            $reqMeta = new \stdClass();
            $reqMeta->meta = new \stdClass();
            $reqMeta->data = new \stdClass();
            $this->requestJson = json_encode($reqMeta);
        }
    }

    protected function _checkAuthToken()
    {
        $this->sessionManager = $this->getServiceLocator()->get('Zend\Session\SessionManager');
        $this->authService = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
        // if session not started and X-Auth-Token set need restart session by id
        $this->authToken = isset($this->headers['X-Auth-Token']) ? $this->headers['X-Auth-Token'] : null;
        if (!empty($this->authToken) && !$this->sessionManager->getId()) {
            $this->sessionManager->setId($this->authToken);
            $this->sessionManager->start();
            $userInfo = $this->authService->getStorage()->read();
        }

        //$this->_checkExpiryDate();
    }

    protected function _getJsonSchemaRequest($method = "index/ping")
    {
        $schemaFile = __DIR__ . '/../../../public/schemas/' . $method . '/request.json';
        $schema = $this->jsonSchemaRetriever->retrieve('file://' . $schemaFile);
        return $schema;
    }

    protected function _requestIsValid($method = "index/ping")
    {
        $schema = $this->_getJsonSchemaRequest($method);
        $data = json_decode($this->requestJson);
        $this->jsonSchemaValidator->check($data, $schema);
        return $this->jsonSchemaValidator->isValid();
    }

    protected function _showBadRequest()
    {
        $this->answer = array(
            'errorMessage' => 'Wrong request params',
            'stack' => $this->jsonSchemaValidator->getErrors()
        );
        return $this->AnswerPlugin()->format($this->answer, 400, 400);
    }

    protected function _showUnauthorisedRequest()
    {
        $this->answer = array(
            'errorMessage' => 'Access denied',
        );
        return $this->AnswerPlugin()->format($this->answer, 401, 401);
    }

    protected function _showCompanyLimitReached($msg = '')
    {
        $this->answer = array(
            'errorMessage' => $msg ? $msg : 'Company Limit reached',
        );
        return $this->AnswerPlugin()->format($this->answer, self::COMPANY_LIMIT_ERROR);
    }

    protected function _showNotFound($msg = '')
    {
        $this->answer = array(
            'errorMessage' => $msg ? $msg : 'Not found',
        );
        return $this->AnswerPlugin()->format($this->answer, self::NOT_FOUND_ERROR);
    }

    protected function _showEmailExists()
    {
        $this->answer = array(
            'errorMessage' => 'Email already in use',
        );
        return $this->AnswerPlugin()->format($this->answer, self::EMAIL_ALREADY_EXISTS_ERROR);
    }
    protected function _showKeyExists($msg = '')
    {
        $this->answer = array(
            'errorMessage' => $msg ? $msg : 'Item with such data already exists',
        );
        return $this->AnswerPlugin()->format($this->answer, self::KEY_ALREADY_EXISTS_ERROR);
    }

    protected function _showEmailInvalid()
    {
        $this->answer = array(
            'errorMessage' => 'Email invalid',
        );
        return $this->AnswerPlugin()->format($this->answer, self::EMAIL_INVALID_ERROR);
    }

    protected function _checkExpiryDate($userId = null)
    {

        $user = null;

        if ($userId === null) {
            if ($this->authService->hasIdentity()) {
                $user = $this->authService->getStorage()->read();
            }
        } else {
            if (is_integer($userId) && $userId > 0) {
                $user = $this->em->find('SafeStartApi\Entity\User', $userId);
            } else {

            }
        }

        if ($user !== null) {
            if (($company = $user->getCompany()) !== null) {
                $now = new \DateTime();
                $expiretyDate = $company->getExpiryDate();
                if ($expiretyDate !== null) {
                    if (is_integer($expiretyDate)) {
                        $now = $now->getTimestamp();
                    } elseif ($expiretyDate instanceof \DateTime) {
                        $now = $now->getTimestamp();
                        $expiretyDate = $expiretyDate->getTimestamp();
                    } elseif (is_string($expiretyDate)) {
                        $now = $now->getTimestamp();
                        $expiretyDate = strtotime($expiretyDate);
                        if (!$expiretyDate) {
                            $expiretyDate = $now;
                        }
                    } else {
                        $expiretyDate = $now;
                    }

                    if ($now >= $expiretyDate) {
                        $this->answer = array(
                            'errorMessage' => 'Date is Expired.',
                        );
                        return $this->AnswerPlugin()->format($this->answer, 400, 400);
                    }
                }
            }
        }
    }
}
