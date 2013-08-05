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

        $user = new User();
        $user->setEmail('test@test.test');
        $user->setUsername('username');
        $user->setLastName('Test User');
        $user->setFirstName('Test User');
        $user->setPlainPassword('12345');
        $manager->persist($user);
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