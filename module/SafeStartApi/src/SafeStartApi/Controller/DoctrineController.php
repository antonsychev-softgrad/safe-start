<?php

namespace SafeStartApi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\Console\Request as ConsoleRequest;
use Zend\Console\Console;
use Zend\Console\Prompt;
use Doctrine\Common\DataFixtures\Loader as FixtureLoader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\ORM\EntityManager;

class DoctrineController extends AbstractActionController
{
    protected $em;
    protected $emTool;
    protected $console;
    protected $fixturesList = array();

    public function onDispatch(MvcEvent $e)
    {
        $request = $this->getRequest();
        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('You can only use this action from a console!');
        }

        $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $this->emTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $this->console = Console::getInstance();

        parent::onDispatch($e);
    }

    public function setDefDataAction()
    {
        $confirm = new Prompt\Confirm('All data will be lost. Are you sure you want to continue? [Y\N]');
        $result = $confirm->show();
        if (!$result) return;

        // Check if the user used --verbose or -v flag
        $verbose = $this->getRequest()->getParam('verbose');

        if ($verbose) {
            $this->console->write("Console output is turned on \r\n", 3);
        }

        //TODO:: get all entities and fixtures
        $classes = array(
            $this->em->getClassMetadata('\SafeStartApi\Entity\User'),
            $this->em->getClassMetadata('\SafeStartApi\Entity\Company'),
            $this->em->getClassMetadata('\SafeStartApi\Entity\Vehicle'),
        );

        if ($verbose) $this->console->write("Drooping DB \r\n", 3);
        $this->emTool->dropSchema($classes);

        if ($verbose) $this->console->write("Creating DB schema \r\n", 3);
        $this->emTool->createSchema($classes);

        if ($verbose) $this->console->write("Loading Fixtures \r\n", 3);
        $this->addFixture(new \SafeStartApi\Fixture\Users());
        $this->addFixture(new \SafeStartApi\Fixture\Vehicles());
        $this->addFixture(new \SafeStartApi\Fixture\Companies());
        $this->loadFixtures();
    }


    protected function addFixture(OrderedFixtureInterface $fixture)
    {
        $this->fixturesList[] = $fixture;
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
