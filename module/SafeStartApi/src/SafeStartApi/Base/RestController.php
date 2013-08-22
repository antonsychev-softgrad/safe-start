<?php

namespace SafeStartApi\Base;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;
use Zend\Session\SessionManager;
use Zend\Session\Container as SessionContainer;

class RestController extends AbstractActionController
{

    const USER_NOT_FOUND_ERROR = 4011;
    const INVALID_CREDENTIAL_ERROR = 4001;
    const USER_ALREADY_LOGGED_IN_ERROR = 4002;
    const EMAIL_ALREADY_EXISTS_ERROR = 4003;
    const EMAIL_INVALID_ERROR = 40004;
    const NOT_FOUND_ERROR = 4004;

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
    }

    protected function _parseRequestFormat()
    {
        $this->requestJson = $this->getRequest()->getContent() ? $this->getRequest()->getContent() : json_encode($this->params()->fromPost());
        $this->headers =  $this->getRequest()->getHeaders()->toArray();
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
    }

    protected function _getJsonSchemaRequest($method = "index/ping") {
        $schemaFile =  __DIR__ . '/../../../public/schemas/' . $method . '/request.json';
        $schema = $this->jsonSchemaRetriever->retrieve('file://' . $schemaFile);
        return $schema;
    }

    protected function _requestIsValid($method = "index/ping") {
        $schema = $this->_getJsonSchemaRequest($method);
        $data = json_decode($this->requestJson);
        $this->jsonSchemaValidator->check($data, $schema);
        return $this->jsonSchemaValidator->isValid();
    }

    protected function _showBadRequest() {
        $this->answer = array(
            'errorMessage' => 'Wrong request params',
            'stack' => $this->jsonSchemaValidator->getErrors()
        );
        return $this->AnswerPlugin()->format($this->answer, 400, 400);
    }

    protected function _showUnauthorisedRequest() {
        $this->answer = array(
            'errorMessage' => 'Access denied',
        );
        return $this->AnswerPlugin()->format($this->answer, 401, 401);
    }

    protected function _showNotFound() {
        $this->answer = array(
            'errorMessage' => 'Not found',
        );
        return $this->AnswerPlugin()->format($this->answer, self::NOT_FOUND_ERROR, 404);
    }

    protected function _showEmailExists() {
        $this->answer = array(
            'errorMessage' => 'Email already in use',
        );
        return $this->AnswerPlugin()->format($this->answer, self::EMAIL_ALREADY_EXISTS_ERROR, 400);
    }

    protected function _showEmailInvalid() {
        $this->answer = array(
            'errorMessage' => 'Email invalid',
        );
        return $this->AnswerPlugin()->format($this->answer, self::EMAIL_INVALID_ERROR, 400);
    }

}
