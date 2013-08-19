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
        $company->setTitle('Company Title 1');
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

        $company->setAdmin($this->getReference('company_admin'));
		$company->addVehicle($this->getReference('vehicle-1'));
        $company->addVehicle($this->getReference('vehicle-2'));
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