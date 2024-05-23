<?php

namespace App\DataFixtures;

use App\Entity\Transaction;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TransactionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 500; $i++) {
            $transaction = new Transaction();
            $transaction->setUser($this->getReference(UserFixtures::USER_REFERENCE));
            $transaction->setWallet($this->getReference(WalletFixtures::WALLET_REFERENCE . rand(0, 9)));
            $transaction->setCategory($this->getReference(CategoryFixtures::CATEGORY_REFERENCE . rand(0, 19)));
            $transaction->setAmount(rand(1, 18437));
            $transaction->setDescription("des" . $i);
            $transaction->setType(rand(1, 2));
            $transaction->setDate($this->randomDate(new DateTime('- 1 month'), new DateTime()));
            $manager->persist($transaction);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            WalletFixtures::class,
            UserFixtures::class,
            CategoryFixtures::class
        ];
    }

    function randomDate(DateTime $startDate, DateTime $endDate): DateTime
    {
        $randomTimestamp = mt_rand($startDate->getTimestamp(), $endDate->getTimestamp());
        return (new DateTime())->setTimestamp($randomTimestamp);
    }
}
