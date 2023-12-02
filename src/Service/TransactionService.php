<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TransactionService
{
    public function __construct(protected Security              $security,
                                protected UserRepository        $userRepository,
                                protected TransactionRepository $transactionRepository
    )
    {
    }

    /** @var User $user */
    public function newAmount(UserInterface $user, Transaction $transaction): void
    {
        //if income than add amount from start
        if ($transaction->isIncome()) {
            $user->setAmount($this->increment($transaction->getAmount()));
        }
        //if expense than minus amount
        if ($transaction->isExpense()) {
            $user->setAmount($this->decrement($transaction->getAmount()));
        }
    }

    /** @var User $user */
    public function editAmount(UserInterface $user, float $oldAmount, Transaction $transaction): void
    {
        $userAmount = $user->getAmount();

        if ($transaction->isExpense()) {
            if ($transaction->getAmount() > $oldAmount) {
                $difference = $transaction->getAmount() - $oldAmount;
                $newAmount = ($difference > 0) ? $userAmount - $difference : $userAmount + abs($difference);
                $user->setAmount($newAmount);
            }

            if ($transaction->getAmount() < $oldAmount) {
                $user->setAmount($userAmount + ($oldAmount - $transaction->getAmount()));
            }
        }
        if ($transaction->isIncome()) {
            $newAmount = ($userAmount > $transaction->getAmount()) ? $userAmount - $transaction->getAmount() : $transaction->getAmount() - $userAmount;
            $user->setAmount($newAmount);
        }


    }

    protected function increment(float $amount): float
    {
        return $this->security->getUser()->getAmount() + $amount;
    }

    protected function decrement(float $amount): float
    {
        return $this->security->getUser()->getAmount() - $amount;
    }

    public function decrementAmount(?float $getAmount): void
    {
        $this->decrement($getAmount);
    }

}