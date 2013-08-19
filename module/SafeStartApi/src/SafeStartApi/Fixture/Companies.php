<?php

namespace SafeStartApi\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SafeStartApi\Entity\Company;

class Companies extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $company = new Company();
        $company->setAdmin($this->getReference('company-admin-user'));
        $company->addResponsiblePerson($this->getReference('responsible-user'));
        $company->addUser($this->getReference('user'));
        $company->addVehicle($this->getReference('vehicle-1'));
        $company->addVehicle($this->getReference('vehicle-2'));
        $company->setTitle('New Company');
        $company->setAddress('Company Address 1');
        $company->setPhone('Company Phone 1');
        $company->setDescription('Company Description 1');
        $company->setRestricted(true);
        $company->setMaxUsers(5);
        $company->setMaxVehicles(10);
        $expiryDate = new \DateTime();
        $expiryDate->setTimestamp(time() + 60*60*366);
        $company->setExpiryDate($expiryDate);
        $manager->persist($company);
        $manager->flush();
    }

    /**
     * Get the order of this execution
     *
     * @return int
     */
    public function getOrder()
    {
        return 3;
    }
}