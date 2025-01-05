<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Wallet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WalletFixture extends Fixture
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5; $i++) {
            $wallet = new Wallet();
            $wallet->setName("Wallet $i");
            $wallet->setUser($this->getReference('user-' . rand(1, 2), User::class));
            $wallet->setAmount(mt_rand(1000, 10000));
            $wallet->setName("Wallet $i");
            $wallet->setCurrency('PLN');
            $wallet->setNumber('PLN');
            $manager->persist($wallet);

            $this->addReference('wallet-' . $i, $wallet);
        }
        $manager->flush();
    }
}