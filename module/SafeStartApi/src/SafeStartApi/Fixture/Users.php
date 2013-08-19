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

        // USER 2
        $user = new User();
        $user->setEmail('test2@test.test');
        $user->setUsername('username');
        $user->setLastName('Test User');
        $user->setFirstName('Test User');
        $user->setPlainPassword('12345');
        $manager->persist($user);
        $manager->flush();

        //Associate a reference for other fixtures
        $this->addReference('user-2', $user);

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

        //Associate a reference for other fixtures
        $this->addReference('super-admin-user', $user);

        // RESPONSIBLE USER
        $user = new User();
        $user->setEmail('responsible@test.test');
        $user->setUsername('responsible');
        $user->setLastName('Responsible');
        $user->setFirstName('User');
        $user->setRole('user');
        $user->setPlainPassword('12345');
        $manager->persist($user);
        $manager->flush();

        //Associate a reference for other fixtures
        $this->addReference('responsible-user', $user);

        // COMPANY ADMIN
        $user = new User();
        $user->setEmail('company@test.test');
        $user->setUsername('company');
        $user->setLastName('Company');
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