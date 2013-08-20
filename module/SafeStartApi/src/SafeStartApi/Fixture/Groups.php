<?php

namespace SafeStartApi\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SafeStartApi\Entity\Group;

class Groups extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $group = new Group();
        $group
            ->setTitle('Daily inspection checklist structural')
            ->setOrder(1)
            ->setVehicle($this->getReference('vehicle-1'))
            ->addField($this->getReference('field-1'))
            ->addField($this->getReference('field-2'))
            ->addField($this->getReference('field-3'))
            ->addField($this->getReference('field-4'))
            ->addField($this->getReference('field-5'));
        $manager->persist($group);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('group-1', $group);

        $group = new Group();
        $group
            ->setTitle('Daily inspection checklist mechanical')
            ->setOrder(2)
            ->setVehicle($this->getReference('vehicle-1'))
            ->addField($this->getReference('field-6'))
            ->addField($this->getReference('field-7'));
        $manager->persist($group);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('group-2', $group);

        $group = new Group();
        $group
            ->setTitle('Crane')
            ->setAdditional(true)
            ->setOrder(3)
            ->setVehicle($this->getReference('vehicle-1'))
            ->addField($this->getReference('field-14'))
            ->addField($this->getReference('field-15'));
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
            ->setVehicle($this->getReference('vehicle-1'))
            ->addField($this->getReference('field-8'))
            ->addField($this->getReference('field-9'))
            ->addField($this->getReference('field-10'))
            ->addField($this->getReference('field-11'))
            ->addField($this->getReference('field-12'))
            ->addField($this->getReference('field-13'));
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
        return 5;
    }
}