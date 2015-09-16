<?php

namespace SafeStartApiTest\Controller;

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
use SafeStartApi\Fixture\Vehicles;
use SafeStartApi\Fixture\Checklists;
use Zend\Stdlib\Parameters;
use Zend\Authentication\AuthenticationService;
use Zend\Session\SessionManager;

class VehicleControllerTest extends HttpControllerTestCase
{
    protected $controller;
    protected $request;
    protected $response;
    protected $routeMatch;
    protected $event;

    protected function setUp()
    {
        $this->addFixtures(new Users());
        $this->addFixtures(new Vehicles());
        $this->addFixtures(new Companies());
        $this->addFixtures(new Fields());
        $this->addFixtures(new Alerts());
        $this->addFixtures(new Checklists());
        parent::setUp();
    }

    public function testGetListActionCanBeAccessed()
    {
        if (!$this->_loginUser('username', '12345')) {
            Bootstrap::$console->write("WARNING: User not logged! \r\n", 2);
        }

        $data = array();
        $this->getRequest()
            ->setMethod('POST')
            ->setContent(json_encode($this->_setApiResponseFormat($data)));

        $this->dispatch('/api/vehicle/getlist');

        $this->assertResponseStatusCode(200);
        $schema = Bootstrap::getJsonSchemaResponse('vehicle/getlist');
        $data = json_decode($this->getResponse()->getContent());
        //print_r($data);
        Bootstrap::$jsonSchemaValidator->check($data, $schema);
        $this->assertTrue(Bootstrap::$jsonSchemaValidator->isValid(), print_r(Bootstrap::$jsonSchemaValidator->getErrors(), true));
    }

    public function testGetDataByIdActionCanBeAccessed()
    {
        if (!$this->_loginUser('username', '12345')) {
            Bootstrap::$console->write("WARNING: User not logged! \r\n", 2);
        }

        $this->getRequest()->setMethod('POST');
        $this->dispatch('/api/vehicle/getlist');
        $data = json_decode($this->getResponse()->getContent());
        $vehicleId = $data->data->vehicles[0]->vehicleId;

        $data = array();

        $this->getRequest()
            ->setMethod('POST')
            ->setContent(json_encode($this->_setApiResponseFormat($data)));

        $this->dispatch('/api/vehicle/' . $vehicleId . '/getinfo');

        $this->assertResponseStatusCode(200);
        $schema = Bootstrap::getJsonSchemaResponse('vehicle/getinfo');
        $data = json_decode($this->getResponse()->getContent());
        //print_r($data);
        Bootstrap::$jsonSchemaValidator->check($data, $schema);
        $this->assertTrue(Bootstrap::$jsonSchemaValidator->isValid(), print_r(Bootstrap::$jsonSchemaValidator->getErrors(), true));
    }

    public function testGetChecklistByVehicleId()
    {
        if (!$this->_loginUser('username', '12345')) {
            Bootstrap::$console->write("WARNING: User not logged! \r\n", 2);
        }

        $this->getRequest()->setMethod('POST');
        $this->dispatch('/api/vehicle/getlist');
        $data = json_decode($this->getResponse()->getContent());
        $vehicleId = $data->data->vehicles[0]->vehicleId;

        $data = array();

        $this->getRequest()
            ->setMethod('POST')
            ->setContent(json_encode($this->_setApiResponseFormat($data)));

        $this->dispatch('/api/vehicle/'.$vehicleId.'/getchecklist');

        $this->assertResponseStatusCode(200);
        $schema = Bootstrap::getJsonSchemaResponse('vehicle/getchecklist');
        $data = json_decode($this->getResponse()->getContent());
        //print_r($data);
        Bootstrap::$jsonSchemaValidator->check($data, $schema);
        $this->assertTrue(Bootstrap::$jsonSchemaValidator->isValid(), print_r(Bootstrap::$jsonSchemaValidator->getErrors(), true));
    }

    public function testGetChecklistByHash()
    {
        $data = array(
            'hash' => 'aaa',
        );

        $this->getRequest()
            ->setMethod('POST')
            ->setContent(json_encode($this->_setApiResponseFormat($data)));

        $this->dispatch('/api/vehicle/getchecklistbyhash');

        $this->assertResponseStatusCode(200);
        $schema = Bootstrap::getJsonSchemaResponse('vehicle/getchecklistbyhash');
        $data = json_decode($this->getResponse()->getContent());
        //print_r($data);
        Bootstrap::$jsonSchemaValidator->check($data, $schema);
        $this->assertTrue(Bootstrap::$jsonSchemaValidator->isValid(), print_r(Bootstrap::$jsonSchemaValidator->getErrors(), true));
    }
/*
    public function testGetAlertsByPeriod()
    {
        if (!$this->_loginUser('username', '12345')) {
            Bootstrap::$console->write("WARNING: User not logged! \r\n", 2);
        }

        $data = array(
            'period' => 60*60*24,
        );

        $this->getRequest()
            ->setMethod('POST')
            ->setContent(json_encode($this->_setApiResponseFormat($data)));

        $this->dispatch('/api/vehicle/getalerts');

        $this->assertResponseStatusCode(200);
        $schema = Bootstrap::getJsonSchemaResponse('vehicle/getalerts');
        $data = json_decode($this->getResponse()->getContent());
        //print_r($data);
        Bootstrap::$jsonSchemaValidator->check($data, $schema);
        $this->assertTrue(Bootstrap::$jsonSchemaValidator->isValid(), print_r(Bootstrap::$jsonSchemaValidator->getErrors(), true));
    }
/*
    public function testCompleteChecklist()
    {
        if (!$this->_loginUser('username', '12345')) {
            Bootstrap::$console->write("WARNING: User not logged! \r\n", 2);
        }

        $this->getRequest()->setMethod('POST');
        $this->dispatch('/api/vehicle/getlist');
        $data = json_decode($this->getResponse()->getContent());
        $vehicleId = $data->data->vehicles[0]->vehicleId;

        $data = array();

        $this->getRequest()
            ->setMethod('POST')
            ->setContent(json_encode($this->_setApiResponseFormat($data)));

        $this->dispatch('/api/vehicle/'.$vehicleId.'/getchecklist');

        $this->assertResponseStatusCode(200);
        $schema = Bootstrap::getJsonSchemaResponse('vehicle/getchecklist');
        $data = json_decode($this->getResponse()->getContent(), true);

        $data = array(
            'emails' => array(
            ),
        );

        $this->getRequest()
            ->setMethod('POST')
            ->setContent(json_encode($this->_setApiResponseFormat($data)));

        $this->dispatch('/api/vehicle/'.$vehicleId.'/completechecklist');

        $this->assertResponseStatusCode(200);
        $schema = Bootstrap::getJsonSchemaResponse('vehicle/completechecklist');
        $data = json_decode($this->getResponse()->getContent());
        //print_r($data);
        Bootstrap::$jsonSchemaValidator->check($data, $schema);
        $this->assertTrue(Bootstrap::$jsonSchemaValidator->isValid(), print_r(Bootstrap::$jsonSchemaValidator->getErrors(), true));

    }

/*
    public function testChecklistToEmail()
    {
        if (!$this->_loginUser('username', '12345')) {
            Bootstrap::$console->write("WARNING: User not logged! \r\n", 2);
        }

        $this->getRequest()->setMethod('POST');
        $this->dispatch('/api/vehicle/getlist');
        $data = json_decode($this->getResponse()->getContent());
        $vehicleId = $data->data->vehicles[0]->vehicleId;

        $data = array(
            'emails' => array(
            ),
        );

        $this->getRequest()
            ->setMethod('POST')
            ->setContent(json_encode($this->_setApiResponseFormat($data)));

        $this->dispatch('/api/vehicle/checklisttoemail');

        $this->assertResponseStatusCode(200);
        $schema = Bootstrap::getJsonSchemaResponse('vehicle/checklisttoemail');
        $data = json_decode($this->getResponse()->getContent());
        //print_r($data);
        Bootstrap::$jsonSchemaValidator->check($data, $schema);
        $this->assertTrue(Bootstrap::$jsonSchemaValidator->isValid(), print_r(Bootstrap::$jsonSchemaValidator->getErrors(), true));
    }
*/
}