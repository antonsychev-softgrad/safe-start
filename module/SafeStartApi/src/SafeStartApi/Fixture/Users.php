<?php
namespace SafeStartApi\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SafeStartApi\Entity\User;

class Users extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // GUEST
        $user = new User();
        $user->setEmail('test@test.test');
        $user->setUsername('username');
        $user->setLastName('Test User');
        $user->setFirstName('Test User');
        $user->setPlainPassword('12345');
        $manager->persist($user);
        $manager->flush();

        $this->addReference('guest', $user);

        // SUPER ADMIN
        $user = new User();
        $user->setEmail('super@test.test');
        $user->setUsername('super');
        $user->setLastName('Super');
        $user->setFirstName('User');
        $user->setRole('superAdmin');
        $user->setPlainPassword('12345');
        $manager->persist($user);
        $manager->flush();

        $this->addReference('super_admin', $user);

        // COMPANY ADMIN
        $user = new User();
        $user->setEmail('company1@test.test');
        $user->setUsername('company1');
        $user->setLastName('Admin');
        $user->setFirstName('Company1');
        $user->setRole('companyAdmin');
        $user->setPlainPassword('12345');
        $manager->persist($user);
        $manager->flush();

        $this->addReference('company_admin', $user);
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