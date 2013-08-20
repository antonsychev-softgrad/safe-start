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
        $field->setTitle('Is the vehicle free of damage?');
        $field->setVehicle($this->getReference('vehicle-1'));
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-1', $field);

        $field = new Field();
        $field->setType('radio');
        $field->setOrder(2);
        $field->setTitle('Are all safety guards in place?');
        $field->setVehicle($this->getReference('vehicle-1'));
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-2', $field);

        $field = new Field();
        $field->setType('radio');
        $field->setOrder(3);
        $field->setTitle('Are the tyres correctly inflated, with good tread and wheel nuts tight?');
        $field->setVehicle($this->getReference('vehicle-1'));
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-3', $field);

        $field = new Field();
        $field->setType('radio');
        $field->setOrder(4);
        $field->setTitle('Are you authorised to inflate or change tyres?');
        $field->setVehicle($this->getReference('vehicle-1'));
        $field->setParent($this->getReference('field-3'));
        $field->setTriggerValue('No');
        $field->setEnabled(true);
        $field->setAdditional(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-4', $field);

        $field = new Field();
        $field->setType('radio');
        $field->setOrder(5);
        $field->setTitle('Is the windscreen and mirrors clean and free of damage?');
        $field->setVehicle($this->getReference('vehicle-1'));
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-5', $field);

        $field = new Field();
        $field->setType('radio');
        $field->setOrder(1);
        $field->setTitle('Have you isolated the vechicle?');
        $field->setVehicle($this->getReference('vehicle-1'));
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-6', $field);

        $field = new Field();
        $field->setType('group');
        $field->setOrder(2);
        $field->setTitle('Are the fluid levels acceptable?');
        $field->setVehicle($this->getReference('vehicle-1'));
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-7', $field);

        $field = new Field();
        $field->setType('checkbox');
        $field->setOrder(3);
        $field->setTitle('Water');
        $field->setVehicle($this->getReference('vehicle-1'));
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-8', $field);

        $field = new Field();
        $field->setType('checkbox');
        $field->setOrder(4);
        $field->setTitle('Hydraulic');
        $field->setVehicle($this->getReference('vehicle-1'));
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-9', $field);

        $field = new Field();
        $field->setType('checkbox');
        $field->setOrder(5);
        $field->setTitle('Brake');
        $field->setVehicle($this->getReference('vehicle-1'));
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-10', $field);

        $field = new Field();
        $field->setType('checkbox');
        $field->setOrder(6);
        $field->setTitle('Coolant');
        $field->setVehicle($this->getReference('vehicle-1'));
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-11', $field);

        $field = new Field();
        $field->setType('checkbox');
        $field->setOrder(7);
        $field->setTitle('Transmission');
        $field->setVehicle($this->getReference('vehicle-1'));
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-12', $field);

        $field = new Field();
        $field->setType('checkbox');
        $field->setOrder(8);
        $field->setTitle('Battery');
        $field->setVehicle($this->getReference('vehicle-1'));
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-13', $field);

        $field = new Field();
        $field->setType('coordinates');
        $field->setOrder(1);
        $field->setTitle('Add vechicle CPS coordinates');
        $field->setVehicle($this->getReference('vehicle-1'));
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-14', $field);

        $field = new Field();
        $field->setType('datePicker');
        $field->setOrder(2);
        $field->setTitle('Add date');
        $field->setVehicle($this->getReference('vehicle-1'));
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-15', $field);

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