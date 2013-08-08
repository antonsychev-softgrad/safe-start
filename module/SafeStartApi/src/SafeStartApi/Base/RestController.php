<?php

namespace SafeStartApi\Base;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;
use Zend\Session\SessionManager;
use Zend\Session\Container as SessionContainer;

class RestController extends AbstractActionController
{
    protected $moduleConfig;

    protected $answer;
    protected $meta;
    protected $data;
    protected $headers;
    protected $requestJson = '';

    public $sessionManager;
    public $authService;
    public $authToken = null;

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
        $this->moduleConfig = $this->getServiceLocator()->get('Config');
    }

    protected function _parseRequestFormat()
    {
        $this->requestJson = $this->getRequest()->getContent() ? $this->getRequest()->getContent() : json_encode($this->params()->fromPost());
        $this->headers =  $this->getRequest()->getHeaders()->toArray();
        $requestData = json_decode($this->requestJson);
        $this->data = isset($requestData->data) ? $requestData->data : null;
        $this->meta = isset($requestData->meta) ? $requestData->meta : null;
        if (is_null($this->data) && is_null($this->meta)) $this->requestJson = json_encode(array('meta'=>array(), 'data'=>array()));
    }

    protected function _checkAuthToken()
    {
        $this->sessionManager = $this->getServiceLocator()->get('Zend\Session\SessionManager');
        $this->authService = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
        // if session not started and X-Auth-Token set need restart session by id
        $this->authToken = isset($this->headers['X-Auth-Token']) ? $this->headers['X-Auth-Token'] : null;
        $logger = $this->getServiceLocator()->get('RequestLogger');
        $logger->debug("RestController Auth Token: " . $this->authToken . "\n");
        $logger->debug("RestController Current SessID: " . $this->sessionManager->getId() . "\n");
        if (!empty($this->authToken) && !$this->sessionManager->getId()) {
            $this->sessionManager->setId($this->authToken);
            $this->sessionManager->start();
            $logger->debug("New Session Id: " . $this->sessionManager->getId() . "\n");
            $userInfo = $this->authService->getStorage()->read();
            $logger->debug("Current User: " . json_encode($userInfo->toArray()) . "\n");
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

}
