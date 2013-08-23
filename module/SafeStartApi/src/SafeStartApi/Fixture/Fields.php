<?php

namespace SafeStartApi\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SafeStartApi\Entity\DefaultField;

class Fields extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        
        $field = new DefaultField();
        $field->setType('radio');
        $field->setOrder(1);
        $field->setTitle('Is the vehicle free of damage?');
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-1', $field);

        $field = new DefaultField();
        $field->setType('radio');
        $field->setOrder(2);
        $field->setTitle('Are all safety guards in place?');
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-2', $field);

        $field = new DefaultField();
        $field->setType('radio');
        $field->setOrder(3);
        $field->setTitle('Are the tyres correctly inflated, with good tread and wheel nuts tight?');
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-3', $field);

        $field = new DefaultField();
        $field->setType('radio');
        $field->setOrder(4);
        $field->setTitle('Are you authorised to inflate or change tyres?');
        $field->setParent($this->getReference('field-3'));
        $field->setTriggerValue('No');
        $field->setEnabled(true);
        $field->setAdditional(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-4', $field);

        $field = new DefaultField();
        $field->setType('radio');
        $field->setOrder(5);
        $field->setTitle('Is the windscreen and mirrors clean and free of damage?');
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-5', $field);

        $field = new DefaultField();
        $field->setType('radio');
        $field->setOrder(1);
        $field->setTitle('Have you isolated the vechicle?');
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-6', $field);

        $field = new DefaultField();
        $field->setType('group');
        $field->setOrder(2);
        $field->setTitle('Are the fluid levels acceptable?');
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-7', $field);

        $field = new DefaultField();
        $field->setType('checkbox');
        $field->setOrder(3);
        $field->setTitle('Water');
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-8', $field);

        $field = new DefaultField();
        $field->setType('checkbox');
        $field->setOrder(4);
        $field->setTitle('Hydraulic');
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-9', $field);

        $field = new DefaultField();
        $field->setType('checkbox');
        $field->setOrder(5);
        $field->setTitle('Brake');
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-10', $field);

        $field = new DefaultField();
        $field->setType('checkbox');
        $field->setOrder(6);
        $field->setTitle('Coolant');
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-11', $field);

        $field = new DefaultField();
        $field->setType('checkbox');
        $field->setOrder(7);
        $field->setTitle('Transmission');
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-12', $field);

        $field = new DefaultField();
        $field->setType('checkbox');
        $field->setOrder(8);
        $field->setTitle('Battery');
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-13', $field);

        $field = new DefaultField();
        $field->setType('coordinates');
        $field->setOrder(1);
        $field->setTitle('Add vechicle CPS coordinates');
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-14', $field);

        $field = new DefaultField();
        $field->setType('datePicker');
        $field->setOrder(2);
        $field->setTitle('Add date');
        $field->setEnabled(true);
        $manager->persist($field);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('field-15', $field);


        $group = new DefaultField();
        $group
            ->setTitle('Daily inspection checklist structural')
            ->setOrder(1);
        $grop
        $this->getReference('field-1')->setGroup($group);
        $this->getReference('field-2')->setGroup($group);
        $this->getReference('field-3')->setGroup($group);
        $this->getReference('field-4')->setGroup($group);
        $this->getReference('field-5')->setGroup($group);
        $manager->persist($group);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('group-1', $group);

        $group = new Group();
        $group
            ->setTitle('Daily inspection checklist mechanical')
            ->setOrder(2)
            ->setVehicle($this->getReference('vehicle-1'));
        $this->getReference('field-6')->setGroup($group);
        $this->getReference('field-7')->setGroup($group);
        $manager->persist($group);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('group-2', $group);

        $group = new Group();
        $group
            ->setTitle('Crane')
            ->setAdditional(true)
            ->setOrder(3)
            ->setVehicle($this->getReference('vehicle-1'));
        $this->getReference('field-14')->setGroup($group);
        $this->getReference('field-15')->setGroup($group);
        $manager->persist($group);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('group-3', $group);

        $group = new Group();
        $group
            ->setTitle('Are the fluid levels acceptable?')
            ->setSubgroup(true)
            ->setParentField($this->getReference('field-7'))
            ->setOrder(1)
            ->setVehicle($this->getReference('vehicle-1'));
        $this->getReference('field-8')->setGroup($group);
        $this->getReference('field-9')->setGroup($group);
        $this->getReference('field-10')->setGroup($group);
        $this->getReference('field-11')->setGroup($group);
        $this->getReference('field-12')->setGroup($group);
        $this->getReference('field-13')->setGroup($group);
        $manager->persist($group);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('subgroup-1', $group);

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