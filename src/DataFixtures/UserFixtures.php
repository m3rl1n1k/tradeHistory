<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user-';

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setCurrency("PLN");
        $user->setEmail('emailTestUser@email.com');
        $user->setPassword('$2y$13$Fp7wIggTVeaSGnGVqUIpveqSswNqGo7VMEERcXHOCv6ugx2Ccb796');
        $this->addReference(self::USER_REFERENCE, $user);
        $manager->persist($user);
        $manager->flush();
    }
}
