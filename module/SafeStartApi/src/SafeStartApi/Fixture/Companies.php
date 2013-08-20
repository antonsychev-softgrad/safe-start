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
        // COMPANY 1
        $company = new Company();
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

        $company->setAdmin($this->getReference('company-admin-user'));
        $company->addResponsibleUser($this->getReference('responsible-user'));

        $this->getReference('usual-user1')->setCompany($company);
        $this->getReference('usual-user2')->setCompany($company);
        $this->getReference('responsible-user')->setCompany($company);
        $this->getReference('vehicle-1')->setCompany($company);
        $this->getReference('vehicle-2')->setCompany($company);

        $manager->flush();
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