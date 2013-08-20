<?php

namespace SafeStartApi\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SafeStartApi\Entity\VehicleType;

class VehicleTypes extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $type = new VehicleType();
        $type->setTitle('Truck');
        $manager->persist($type);
        $manager->flush();
        //Associate a reference for other fixtures
        $this->addReference('truck-type-1', $type);

    }

    /**
     * Get the order of this execution
     *
     * @return int
     */
    public function getOrder()
    {
        return 2;
    }
}