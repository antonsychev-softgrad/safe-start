<?php

namespace SafeStartApiTest\Controller;

use SafeStartApiTest\Bootstrap;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use SafeStartApiTest\Base\HttpControllerTestCase;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use SafeStartApiTest\Fixtures\LoadUsersData;
use Zend\Stdlib\Parameters;
use Zend\Authentication\AuthenticationService;
use Zend\Session\SessionManager;

class UserDataControllerTest extends HttpControllerTestCase
{
    protected $controller;
    protected $request;
    protected $response;
    protected $routeMatch;
    protected $event;

    protected function setUp()
    {
        $this->addFixtures(new LoadUsersData());
        parent::setUp();
    }

    public function testVehiclesListActionCanBeAccessed()
    {
        /*
        $this->getRequest()
            ->setMethod('POST')
            ->setPost(new Parameters(array(
                'username' => 'username',
                'password' => '12345',
             )));
        */
        $this->dispatch('/api/userdata/vehicleslist');

        $this->assertResponseStatusCode(200);
        $schema = Bootstrap::getJsonSchemaResponse('userdata/vehicleslist');
        $data = json_decode($this->getResponse()->getContent());
        //print_r($data);
        Bootstrap::$jsonSchemaValidator->check($data, $schema);
        $this->assertTrue(Bootstrap::$jsonSchemaValidator->isValid(), print_r(Bootstrap::$jsonSchemaValidator->getErrors(), true));
    }

    public function testVehicleDataActionCanBeAccessed()
    {
        /*
        $this->getRequest()
            ->setMethod('POST')
            ->setPost(new Parameters(array(
                'username' => 'username',
                'password' => '12345',
             )));
        */
        $this->dispatch('/api/userdata/vehicledata');

        $this->assertResponseStatusCode(200);
        $schema = Bootstrap::getJsonSchemaResponse('userdata/vehicledata');
        $data = json_decode($this->getResponse()->getContent());
        //print_r($data);
        Bootstrap::$jsonSchemaValidator->check($data, $schema);
        $this->assertTrue(Bootstrap::$jsonSchemaValidator->isValid(), print_r(Bootstrap::$jsonSchemaValidator->getErrors(), true));
    }
}