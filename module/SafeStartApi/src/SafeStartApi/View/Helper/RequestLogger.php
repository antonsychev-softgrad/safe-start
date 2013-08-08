<?php

namespace SafeStartApi\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceManager;
use Zend\Config\Writer\Yaml as YamlWriter;

/**
 * Class RequestLogger
 * @package SafeStartApi\View\Helper
 */
class RequestLogger extends AbstractHelper
{
    /**
     * Service Locator
     * @var ServiceManager
     */
    protected $serviceLocator;

    /**
     * @var
     */
    protected $meta;
    /**
     * @var
     */
    protected $data;
    /**
     * @var
     */
    protected $headers;
    /**
     * @var
     */
    protected $requestJson;
    /**
     * @var
     */
    protected $requestId;

    /**
     * @var
     */
    protected $writer;

    /**
     * __invoke
     *
     * @access public
     * @internal param int $value
     * @param $value
     * @return String
     */
    public function __invoke($value)
    {
        $logger = $this->serviceLocator->get('RequestLogger');
        $request = $this->serviceLocator->get('Request');
        $this->requestJson = $request->getContent() ? $request->getContent() : json_encode($request->getPost());
        $this->headers = $request->getHeaders()->toArray();
        $requestData = json_decode($this->requestJson);
        $this->data = isset($requestData->data) ? $requestData->data : null;
        $this->meta = isset($requestData->meta) ? $requestData->meta : null;
        if (isset($this->headers['X-Request-Id'])) $this->requestId = $this->headers['X-Request-Id'];
        if (!empty($this->meta) && isset($this->meta->requestId)) $this->requestId = $this->meta->requestId;

        $logger->debug("\n\n\n============[". $this->requestId ."]==================\n");
        $logger->debug("New " . $request->getMethod() . " request to " . $request->getRequestUri());
        //log headers
        if (function_exists('yaml_emit')) {
            $writer = new YamlWriter();
            $logger->debug("Headers:\n" . $writer->toString($this->headers));
        } else {
            $logger->debug("Headers:\n" . json_encode($this->headers));
        }
        // log POST data
        if ($request->getMethod() == 'POST') {
            if (function_exists('yaml_emit')) {
                $writer = new YamlWriter();
                $logger->debug("POST data:\n" . $writer->toString(json_decode($this->requestJson, true)));
            } else {
                $logger->debug("POST data:\n" . $this->requestJson);
            }
        }
        // log response
        if (function_exists('yaml_emit')) {
            $writer = new YamlWriter();
            $logger->debug("Response:\n" . $writer->toString($value));
        } else {
            $logger->debug("Response:\n" . json_encode($value));
        }

        return true;
    }

    /**
     * Setter for $serviceLocator
     * @param ServiceManager $serviceLocator
     */
    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
}