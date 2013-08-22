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
        $vehicle->setTitle('Mitsubishi');
        $vehicle->setType('Crossover');
        $vehicle->setProjectName('Project name');
        $vehicle->setProjectNumber(123);
        $vehicle->setServiceDueKm(100);
        $vehicle->setServiceDueHours(100);
        $expiryDate = new \DateTime();
        $expiryDate->setTimestamp(time() + 60*60*366);
        $vehicle->setExpiryDate($expiryDate);
        $manager->persist($vehicle);
        $manager->flush();

        $vehicle->addResponsibleUser($this->getReference('responsible-user'));
        $vehicle->addUser($this->getReference('usual-user1'));
        $manager->flush();

        //Associate a reference for other fixtures
        $this->addReference('vehicle-1', $vehicle);



        $vehicle = new Vehicle();
        $vehicle->setPlantId('BBDS-CHJDJ-1234');
        $vehicle->setRegistrationNumber('REGNUMBER2');
        $vehicle->setTitle('Ford');
        $vehicle->setType('Utility truck');
        $vehicle->setProjectName('Project name');
        $vehicle->setProjectNumber(123);
        $vehicle->setServiceDueKm(100);
        $vehicle->setServiceDueHours(100);
        $expiryDate = new \DateTime();
        $expiryDate->setTimestamp(time() + 60*60*366);
        $vehicle->setExpiryDate($expiryDate);
        $manager->persist($vehicle);
        $manager->flush();

        $vehicle->addResponsibleUser($this->getReference('responsible-user'));
        $vehicle->addUser($this->getReference('usual-user2'));

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