<?php

namespace SafeStartApi\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SafeStartApi\Entity\Alert;

class Alerts extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $alert = new Alert();
        $alert->setTitle('Do not work on tyres unless authorised');
        $alert->setTriggerValue('No');
        $alert->setField($this->getReference('field-4'));
        $manager->persist($alert);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('alert-1', $alert);

        $alert = new Alert();
        $alert->setTitle('Isolate vehicle before continuing');
        $alert->setTriggerValue('No');
        $alert->setField($this->getReference('field-6'));
        $manager->persist($alert);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('alert-2', $alert);

    }

    /**
     * Get the order of this execution
     *
     * @return int
     */
    public function getOrder()
    {
        return 6;
    }
}