<?php
namespace SafeStartApiTest\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SafeStartApi\Entity\Vehicle;

class LoadVehicleData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $vehicle = new Vehicle();
        $vehicle->setPlantId('ACHJDJ34234');
        $vehicle->setRegistrationNumber('REGNUMBER');
        $vehicle->setVehicleName('Name');
        $vehicle->setProjectName('Project name');
        $vehicle->setProjectNumber(123);
        $vehicle->setKmsUntilNext(100);
        $vehicle->setHoursUntilNext(100);
        $vehicle->setExpiryDate(new \DateTime());
        $manager->persist($vehicle);
        $manager->flush();
    }

    /**
     * Get the order of this execution
     *
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }
}