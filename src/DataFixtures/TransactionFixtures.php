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
        for ($i = 0; $i < 500; $i++) {
            $wallet = $this->getReference("wallet-" . rand(1, 2), Wallet::class);
            $category = $this->getReference("category-" . rand(1, 5), Category::class);
            $user = $this->getReference('user-1', User::class);
            $this->createTransaction($i, $user, $wallet, $category, $manager);
        }
        for ($i = 0; $i < 500; $i++) {
            $wallet = $this->getReference("wallet-" . rand(3, 4), Wallet::class);
            $category = $this->getReference("category-" . rand(5, 9), Category::class);
            $user = $this->getReference('user-2', User::class);
            $this->createTransaction($i, $user, $wallet, $category, $manager);
        }
        $manager->flush();
    }

    private function createTransaction($i, $user, $wallet, $category, $manager): void
    {
        $transaction = new Transaction();
        $transaction->setUser($user);
        $transaction->setDate(new DateTime());
        $transaction->setDescription("Description transaction " . $i);
        $transaction->setWallet($wallet);
        $transaction->setCategory($category);
        $price = rand(1, $wallet->getAmount()) . '.' . rand(0, 99);
        $transaction->setAmount((float)$price);
        $transaction->setType(rand(1, 2));
        $manager->persist($transaction);
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