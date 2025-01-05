<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\Wallet;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TransactionFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 4100; $i++) {
            $transaction = new Transaction();
            $transaction->setUser($this->getReference('user-' . rand(1, 2), User::class));
            $transaction->setDate(new DateTime());
            $transaction->setDescription("Description transaction " . $i);
            $transaction->setWallet($this->getReference("wallet-" . rand(1, 4), Wallet::class));
            $transaction->setCategory($this->getReference("category-" . rand(1, 9), Category::class));
            $price = rand(1, 10000) . ',' . rand(0, 99);
            $transaction->setAmount((float)$price);
            $transaction->setType(rand(1, 2));
            $manager->persist($transaction);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            WalletFixture::class,
            CategoryFixture::class
        ];
    }
}