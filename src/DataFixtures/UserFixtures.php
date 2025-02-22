<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $user = $this->newUser('bisix21@gmail.com', 1);
        $manager->persist($user);
        
        $user2 = $this->newUser('bisix21@gmail.coma', 2);
        $manager->persist($user2);

        $user3 = $this->newUser('feedback@email.com', roles: ['ROLE_FEEDBACK_CHECKER']);
        $manager->persist($user3);

        $manager->flush();
    }

    private function newUser($email, int $index = null, array $roles = []): User
    {
        $user = new User();
        $user->setCurrency("PLN");
        $user->setEmail($email);
        $user->setPassword('$2y$13$Fp7wIggTVeaSGnGVqUIpveqSswNqGo7VMEERcXHOCv6ugx2Ccb796');
        $user->setSetting(null);
        $user->setRoles($roles);
        if ($index !== null) {
            $this->addReference('user-' . $index, $user);
        }
        return $user;

    }
}