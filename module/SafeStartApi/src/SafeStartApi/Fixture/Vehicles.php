<?php

namespace SafeStartApi\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SafeStartApi\Entity\Vehicle;

class Vehicles extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $vehicle = new Vehicle();
        $vehicle->setPlantId('ACHJ-DJ34-A234');
        $vehicle->setRegistrationNumber('REGNUMBER1');
        $vehicle->setVehicleName('Mitsubishi');
        $vehicle->setProjectName('Project name');
        $vehicle->setProjectNumber(123);
        $vehicle->setKmsUntilNext(100);
        $vehicle->setHoursUntilNext(100);
        $vehicle->setExpiryDate(new \DateTime());
        $manager->persist($vehicle);
        $manager->flush();

        $vehicle->setResponsibleUser($this->getReference('responsible-user'));
        $vehicle->addEndUser($this->getReference('usual-user1'));
        $manager->flush();

        //Associate a reference for other fixtures
        $this->addReference('vehicle-1', $vehicle);



        $vehicle = new Vehicle();
        $vehicle->setPlantId('BBDS-CHJDJ-1234');
        $vehicle->setRegistrationNumber('REGNUMBER2');
        $vehicle->setVehicleName('Ford');
        $vehicle->setProjectName('Project name');
        $vehicle->setProjectNumber(123);
        $vehicle->setKmsUntilNext(100);
        $vehicle->setHoursUntilNext(100);
        $vehicle->setExpiryDate(new \DateTime());
        $manager->persist($vehicle);
        $manager->flush();

        $vehicle->setResponsibleUser($this->getReference('responsible-user'));
        $vehicle->addEndUser($this->getReference('usual-user2'));

        $manager->flush();

        //Associate a reference for other fixtures
        $this->addReference('vehicle-2', $vehicle);

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