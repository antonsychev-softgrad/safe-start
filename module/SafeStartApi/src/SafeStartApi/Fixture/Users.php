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
        $user->setEmail('test1@test.test');
        $user->setUsername('username');
        $user->setLastName('Test User');
        $user->setFirstName('Test User');
        $user->setPlainPassword('12345');
        $user->setRole('companyUser');
        $manager->persist($user);
        $manager->flush();

        //Associate a reference for other fixtures
        $this->addReference('usual-user1', $user);

        // USER 1
        $user = new User();
        $user->setEmail('test2@test.test');
        $user->setUsername('username2');
        $user->setLastName('Test2 User');
        $user->setFirstName('Test2 User');
        $user->setPlainPassword('12345');
        $user->setRole('companyUser');
        $manager->persist($user);
        $manager->flush();

        //Associate a reference for other fixtures
        $this->addReference('usual-user2', $user);

        // SUPER ADMIN
        $user = new User();
        $user->setEmail('responsible@test.test');
        $user->setUsername('responsible');
        $user->setLastName('Super');
        $user->setFirstName('User');
        $user->setRole('superManager');
        $user->setPlainPassword('12345');
        $manager->persist($user);
        $manager->flush();

        //Associate a reference for other fixtures
        $this->addReference('responsible-user', $user);

        $user = new User();
        $user->setEmail('super@test.test');
        $user->setUsername('super');
        $user->setLastName('SuperAdmin');
        $user->setFirstName('User');
        $user->setRole('companyAdmin');
        $user->setPlainPassword('12345');
        $manager->persist($user);
        $manager->flush();

        //Associate a reference for other fixtures
        $this->addReference('company-admin-user', $user);
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