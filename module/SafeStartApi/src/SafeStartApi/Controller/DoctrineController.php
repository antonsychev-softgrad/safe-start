<?php

namespace SafeStartApi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;
use Zend\Console\Console;
use Doctrine\Common\DataFixtures\Loader as FixtureLoader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\ORM\EntityManager;

class DoctrineController extends AbstractActionController
{
    protected $em;
    protected $console;
    protected $fixturesList = array();

    public function updateAction() {

    }

    public function setDefDataAction()
    {
        $request = $this->getRequest();

        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('You can only use this action from a console!');
        }

        // Check if the user used --verbose or -v flag
        $verbose = $request->getParam('verbose');

        $this->console = Console::getInstance();

        if ($verbose) {
            $this->console->write("Console output is turned on \r\n", 3);
        }

        $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

    }


    protected function addFixtures($fixtures)
    {
        $this->fixturesList[] = $fixtures;
    }

    protected function loadFixtures()
    {
        if (!empty($this->fixturesList)) {
            $loader = new FixtureLoader();
            foreach ($this->fixturesList as $fixtures) {
                $loader->addFixture($fixtures);
            }
            $purger = new ORMPurger();
            $executor = new ORMExecutor($this->em, $purger);
            $executor->execute($loader->getFixtures());
        }
    }

}
