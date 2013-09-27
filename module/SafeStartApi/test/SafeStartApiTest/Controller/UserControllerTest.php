<?php

namespace SafeStartApiTest\Controller;

use Doctrine\Tests\Models\Generic\BooleanModel;
use SafeStartApiTest\Bootstrap;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use SafeStartApiTest\Base\HttpControllerTestCase;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use SafeStartApi\Fixture\Users;
use SafeStartApi\Fixture\Alerts;
use SafeStartApi\Fixture\Companies;
use SafeStartApi\Fixture\Fields;
use SafeStartApi\Fixture\Groups;
use SafeStartApi\Fixture\Vehicles;
use Zend\Stdlib\Parameters;

class UserControllerTest extends HttpControllerTestCase
{
    protected $controller;
    protected $request;
    protected $response;
    protected $routeMatch;
    protected $event;

    protected function setUp()
    {
        $this->addFixtures(new Users());
        parent::setUp();
    }

    public function testLoginAction()
    {
        if (!$this->_loginUser('username', '12345')) {
            Bootstrap::$console->write("WARNING: User not logged! \r\n", 2);
        }

        $this->dispatch('/api/user/login');

        $this->assertResponseStatusCode(200);

        $data = json_decode($this->getResponse()->getContent());
        print_r($data);
        $schema = Bootstrap::getJsonSchemaResponse('user/login');
        Bootstrap::$jsonSchemaValidator->check($data, $schema);
        $this->assertTrue(Bootstrap::$jsonSchemaValidator->isValid(), print_r(Bootstrap::$jsonSchemaValidator->getErrors(), true));
        $this->assertTrue($data->meta->errorCode === \SafeStartApi\Base\RestController::USER_ALREADY_LOGGED_IN_ERROR);

    }

    public function testDeletedUserLoginAction()
    {
        if (!$this->_loginUser('username3', '12345')) {
            Bootstrap::$console->write("WARNING: User not logged! \r\n", 2);
        }

        $this->dispatch('/api/user/login');

        $this->assertResponseStatusCode(200);

        $data = json_decode($this->getResponse()->getContent());
        //print_r($data);
        $schema = Bootstrap::getJsonSchemaResponse('user/login');
        Bootstrap::$jsonSchemaValidator->check($data, $schema);
        $this->assertTrue(Bootstrap::$jsonSchemaValidator->isValid(), print_r(Bootstrap::$jsonSchemaValidator->getErrors(), true));
        $this->assertTrue($data->meta->errorCode === \SafeStartApi\Base\RestController::USER_ALREADY_LOGGED_IN_ERROR);

    }

    public function testNotEnabledLoginAction()
    {
        if (!$this->_loginUser('username4', '12345')) {
            Bootstrap::$console->write("WARNING: User not logged! \r\n", 2);
        }

        $this->dispatch('/api/user/login');

        $this->assertResponseStatusCode(200);

        $data = json_decode($this->getResponse()->getContent());
        //print_r($data);
        $schema = Bootstrap::getJsonSchemaResponse('user/login');
        Bootstrap::$jsonSchemaValidator->check($data, $schema);
        $this->assertTrue(Bootstrap::$jsonSchemaValidator->isValid(), print_r(Bootstrap::$jsonSchemaValidator->getErrors(), true));
        $this->assertTrue($data->meta->errorCode === \SafeStartApi\Base\RestController::USER_ALREADY_LOGGED_IN_ERROR);

    }

}