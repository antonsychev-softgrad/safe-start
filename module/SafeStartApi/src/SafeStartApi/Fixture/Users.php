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
        // USER 1
        $user = new User();
        $user->setEmail('usual-user1@test.test');
        $user->setUsername('username');
        $user->setLastName('Company');
        $user->setFirstName('User 1');
        $user->setPlainPassword('12345');
        $user->setRole('companyUser');
        $manager->persist($user);
        $manager->flush();

        //Associate a reference for other fixtures
        $this->addReference('usual-user1', $user);

        // USER 1
        $user = new User();
        $user->setEmail('usual-user2@test.test');
        $user->setUsername('username2');
        $user->setLastName('Company');
        $user->setFirstName('User 2');
        $user->setPlainPassword('12345');
        $user->setRole('companyUser');
        $manager->persist($user);
        $manager->flush();

        //Associate a reference for other fixtures
        $this->addReference('usual-user2', $user);

        // COMPANY MANAGER
        $user = new User();
        $user->setEmail('responsible-user@test.test');
        $user->setUsername('responsible');
        $user->setLastName('Responsible');
        $user->setFirstName('User');
        $user->setRole('companyManager');
        $user->setPlainPassword('12345');
        $manager->persist($user);
        $manager->flush();

        //Associate a reference for other fixtures
        $this->addReference('responsible-user', $user);

        // COMPANY OWNER
        $user = new User();
        $user->setEmail('company-admin-user@test.test');
        $user->setUsername('company-admin');
        $user->setLastName('Company Admin');
        $user->setFirstName('User');
        $user->setRole('companyAdmin');
        $user->setPlainPassword('12345');
        $manager->persist($user);
        $manager->flush();

        //Associate a reference for other fixtures
        $this->addReference('company-admin-user', $user);

        // SUPER USER
        $user = new User();
        $user->setEmail('super-user@test.test');
        $user->setUsername('super');
        $user->setLastName('Super Admin');
        $user->setFirstName('User');
        $user->setRole('superAdmin');
        $user->setPlainPassword('12345');
        $manager->persist($user);
        $manager->flush();

        //Associate a reference for other fixtures
        $this->addReference('super-user', $user);
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