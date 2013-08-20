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
        $alert->setField($this->getReference('additional-field-1'));
        $manager->persist($alert);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('alert-1', $alert);

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