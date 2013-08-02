<?php

namespace SafeStartApiTest\Base;

use SafeStartApiTest\Bootstrap;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\ORM\EntityManager;
use Zend\Session\SessionManager;
use Zend\Authentication\AuthenticationService;

class HttpControllerTestCase extends AbstractHttpControllerTestCase
{
    protected $fixturesList = array();

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public $em;

    protected function setUp()
    {
        parent::setUp();

        $this->setApplicationConfig(
            Bootstrap::getConfig()
        );

        $serviceManager = Bootstrap::getServiceManager();
        $this->em = $serviceManager->get('doctrine.entitymanager.orm_default');
        //$this->em->beginTransaction();
        $this->loadFixtures();
    }

    protected function addFixtures($fixtures)
    {
        $this->fixturesList[] = $fixtures;
    }

    protected function loadFixtures()
    {
        if (!empty($this->fixturesList)) {
            $loader = new Loader();
            foreach ($this->fixturesList as $fixtures) {
                $loader->addFixture($fixtures);
            }
            $purger = new ORMPurger();
            $executor = new ORMExecutor($this->em, $purger);
            $executor->execute($loader->getFixtures());
        }
    }

    protected function _loginUser($username, $password) {
        $this->dispatch('/api/user/login', 'POST', array(
            'username' => $username,
            'password' => $password,
        ));

        $auth = new AuthenticationService();

        return $auth->hasIdentity();
    }


    /**
     * Shuts the kernel down if it was used in the test.
     */
    protected function tearDown()
    {
        //$this->em->rollback();
    }
}