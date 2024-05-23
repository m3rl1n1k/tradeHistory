<?php

namespace App\DataFixtures;

use App\Entity\Wallet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class WalletFixtures extends Fixture implements DependentFixtureInterface
{
    public const WALLET_REFERENCE = 'wallet-';

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {

            $wallet = new Wallet();
            $wallet->setName('wallet' . $i);
            $wallet->setUser($this->getReference(UserFixtures::USER_REFERENCE));
            $wallet->setAmount(rand(1000, 1000000));
            $wallet->setCurrency("PLN");
            $wallet->setNumber($wallet->getCurrency());
            $this->addReference(self::WALLET_REFERENCE . $i, $wallet);
            $manager->persist($wallet);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}
