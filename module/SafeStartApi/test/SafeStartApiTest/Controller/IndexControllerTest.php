<?php

namespace SafeStartApiTest\Controller;

use SafeStartApiTest\Bootstrap;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

class IndexControllerTest extends AbstractHttpControllerTestCase
{
    protected $controller;
    protected $request;
    protected $response;
    protected $routeMatch;
    protected $event;

    protected function setUp()
    {
        $this->setApplicationConfig(
            Bootstrap::getConfig()
        );
        parent::setUp();

    }

    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/api/');
        $this->assertResponseStatusCode(200);
        $schema = Bootstrap::getJsonSchemaResponse('index/ping');
        $data = json_decode($this->getResponse()->getContent());
        Bootstrap::$jsonSchemaValidator->check($data, $schema);
        $this->assertTrue(Bootstrap::$jsonSchemaValidator->isValid(), print_r(Bootstrap::$jsonSchemaValidator->getErrors(), true));
    }
}