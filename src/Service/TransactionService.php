<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TransactionService
{
    public function __construct(protected Security       $security,
                                protected UserRepository $userRepository
    )
    {
    }

    /** @var User $user */
    public function newAmount(UserInterface $user, Transaction $transaction): void
    {
        //if income than add amount from start
        if ($transaction->getType() === Transaction::INCOMING) {
            $user->setAmount($user->getAmount() + $transaction->getAmount());
        }
        //if expense than minus amount
        if ($transaction->getType() === Transaction::EXPENSE) {
            $user->setAmount($user->getAmount() - $transaction->getAmount());
        }
    }

    /** @var User $user */
    public function editAmount(UserInterface $user, float $newAmount, Transaction $transaction): void
    {
        //if old amount less than new then from user amount minus different.
        if ($newAmount > $transaction->getAmount()) {
            $different = $transaction->getAmount() - $newAmount;
            $user->setAmount($user->getAmount() - $different);
        }

        //if old amount bigger that new then to user amount add different.
        if ($newAmount < $transaction->getAmount()) {
            $different = $newAmount - $user->getAmount();
            $user->setAmount($user->getAmount() + $different);
        }
    }
}