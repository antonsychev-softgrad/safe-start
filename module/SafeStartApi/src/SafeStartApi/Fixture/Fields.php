<?php

namespace SafeStartApi\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SafeStartApi\Entity\Field;

class Fields extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $field = new Field();
        $field->setType('radio');
        $field->setOrder(1);
        $field->setLabel('Is the vechicle free of damage?');
        $field->setVehicle($this->getReference('vehicle-1'));
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-1', $field);

        $field = new Field();
        $field->setType('radio');
        $field->setOrder(2);
        $field->setLabel('Are all safety guards in place?');
        $field->setVehicle($this->getReference('vehicle-1'));
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-2', $field);

    }

    /**
     * Get the order of this execution
     *
     * @return int
     */
    public function getOrder()
    {
        return 4;
    }
}