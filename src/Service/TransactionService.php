<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;

class TransactionService
{
    public function __construct(protected Security       $security,
                                protected UserRepository $userRepository
    )
    {
    }

    public function newAmount(Transaction $transaction): void
    {
        /** @var User $user */
        $user = $this->security->getUser();

        //if income than add amount from start
        if ($transaction->getType() === Transaction::INCOMING) {
            $user->setAmount($user->getAmount() + $transaction->getAmount());
        }
        //if expense than minus amount
        if ($transaction->getType() === Transaction::EXPENSE) {
            $user->setAmount($user->getAmount() - $transaction->getAmount());
        }
    }

    public function editAmount(float $newAmount, Transaction $transaction): void
    {
        $transaction = $transaction;
        $a = 1;
        //if old amount bigger that new then to user amount add different.

        //if old amount less than new then from user amount minus different.
    }
}