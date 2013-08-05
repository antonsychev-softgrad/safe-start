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
    protected $requestId = 'request_id_not_set';

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
        $this->_parseRequestFromat();
        $this->_checkAuthToken();
        $this->moduleConfig = $this->getServiceLocator()->get('Config');
    }

    protected function _parseRequestFromat()
    {
        $this->requestJson = $this->getRequest()->getContent() ? $this->getRequest()->getContent() : json_encode($this->params()->fromPost());
        $this->headers = $this->params()->fromHeader();
        $requestData = json_decode($this->requestJson);
        $this->data = isset($requestData->data) ? $requestData->data : null;
        $this->meta = isset($requestData->meta) ? $requestData->meta : null;
        if (isset($this->headers['X-Request-Id'])) $this->requestId = $this->headers['X-Request-Id'];
        if (!empty($this->meta) && isset($this->meta->requestId)) $this->requestId = $this->meta->requestId;

    }

    protected function _checkAuthToken()
    {
        $this->sessionManager = $this->getServiceLocator()->get('Zend\Session\SessionManager');
        $this->authService = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
        // if session not started and X-Auth-Token set need restart session by id
        $this->authToken = isset($this->headers['X-Auth-Token']) ? $this->headers['X-Auth-Token'] : null;
        if (!empty($authToken) && !$this->authService->hasIdentity()) {
            $this->sessionManager->setId($this->authToken);
            $this->sessionManager->start();
        }
    }

    protected function _getJsonSchemaRequest($method = "index/ping") {
        $schemaFile =  __DIR__ . '/../../../public/schemas/' . $method . '/request.json';
        $schema = $this->jsonSchemaRetriever->retrieve('file://' . $schemaFile);
        return $schema;
    }

    protected function _requestIsValide($method = "index/ping") {
        $schema = $this->_getJsonSchemaRequest($method);
        $data = json_decode($this->requestJson);
        $this->jsonSchemaValidator->check($data, $schema);
        return $this->jsonSchemaValidator->isValid();
    }

    protected function _showBadRequest() {
        $this->answer = array(
            'errorMessage' => 'Wrong requesr params',
            'stack' => $this->jsonSchemaValidator->getErrors()
        );
        return $this->AnswerPlugin()->format($this->answer, 400, 400);
    }

}
