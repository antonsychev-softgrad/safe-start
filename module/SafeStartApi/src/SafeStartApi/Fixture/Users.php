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

        // SUPER USER
        $user = new User();
        $user->setEmail('super-user@test.test');
        $user->setUsername('super');
        $user->setLastName('Super Admin');
        $user->setFirstName('User');
        $user->setRole('superAdmin');
        $user->setPlainPassword('12345');
        $user->setEnabled(1);
        $manager->persist($user);
        $manager->flush();

        //Associate a reference for other fixtures
        $this->addReference('super-user', $user);

        // USER 1
        $user = new User();
        $user->setEmail('usual-user1@test.test');
        $user->setUsername('username');
        $user->setLastName('Company');
        $user->setFirstName('User 1');
        $user->setPlainPassword('12345');
        $user->setRole('companyUser');
        $user->setEnabled(1);
        $manager->persist($user);
        $manager->flush();

        //Associate a reference for other fixtures
        $this->addReference('usual-user1', $user);

        // USER 2
        $user = new User();
        $user->setEmail('usual-user2@test.test');
        $user->setUsername('username2');
        $user->setLastName('Company');
        $user->setFirstName('User 2');
        $user->setPlainPassword('12345');
        $user->setRole('companyUser');
        $user->setEnabled(1);
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
        $user->setEnabled(1);
        $user->setDevice('android');
        $user->setDeviceId('APA91bE6LvMXz3Nnb3YuuTmx34aPp1Gj7U4BpAO_Q4vHtXRVPiVYNplzmOLwe5Y-rc68w6ZZre_I4dkHYnf5kf23IYarNDGvlT7Zvf56VZQ7I4c9uXAxkPkzBM2NhtIV77M4gr8hyBtlO-QPi3xXHNWL7OGZcvevXw');
        $manager->persist($user);
        $manager->flush();

        //Associate a reference for other fixtures
        $this->addReference('responsible-user', $user);


        // COMPANY MANAGER
        $user = new User();
        $user->setEmail('responsible-user2@test.test');
        $user->setUsername('responsible2');
        $user->setLastName('Responsible');
        $user->setFirstName('User 2');
        $user->setRole('companyManager');
        $user->setPlainPassword('12345');
        $user->setEnabled(1);
        $user->setDevice('ios');
        $user->setDeviceId('0f744707bebcf74f9b7c25d48e3358945f6aa01da5ddb387462c7eaf61bbad78');
        $manager->persist($user);
        $manager->flush();

        //Associate a reference for other fixtures
        $this->addReference('responsible-user2', $user);

        // COMPANY OWNER
        $user = new User();
        $user->setEmail('company-admin-user@test.test');
        $user->setUsername('company-admin');
        $user->setLastName('Company Admin');
        $user->setFirstName('User');
        $user->setRole('companyAdmin');
        $user->setPlainPassword('12345');
        $user->setEnabled(1);
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