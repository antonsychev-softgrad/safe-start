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
        $alert->setDescription('Description of alert 1');
        $alert->setField($this->getReference('field-4'));
        $alert->setVehicle($this->getReference('vehicle-1'));
        $alert->setChecklist($this->getReference('checklist-1'));
        $manager->persist($alert);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('alert-1', $alert);

        $alert = new Alert();
        $alert->setDescription('Description of alert 2');
        $alert->setField($this->getReference('field-6'));
        $alert->setVehicle($this->getReference('vehicle-2'));
        $alert->setChecklist($this->getReference('checklist-2'));
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