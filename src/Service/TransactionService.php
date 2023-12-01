<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TransactionService
{
    private UserInterface $user;

    public function __construct(protected Security       $security,
                                protected UserRepository $userRepository,
                                protected EntityManagerInterface   $entityManager
    )
    {
        $this->user = $this->security->getUser();
    }

    public function newAmount(Transaction $transaction): void
    {
        //if income than add amount from start
        if ($transaction->getType() === Transaction::INCOMING) {
            $this->user->setAmount($this->user->getAmount() + $transaction->getAmount());
        }
        //if expense than minus amount
        if ($transaction->getType() === Transaction::EXPENSE) {
            $this->user->setAmount($this->user->getAmount() - $transaction->getAmount());
        }
    }

    public function editAmount(float $newAmount, Transaction $transaction): void
    {
        //if old amount less than new then from user amount minus different.
        if ($newAmount > $transaction->getAmount()) {
            $different = $transaction->getAmount() - $newAmount;
            $this->user->setAmount($this->user->getAmount() - $different);
        }

        //if old amount bigger that new then to user amount add different.
        if ($newAmount < $transaction->getAmount()) {
            $different = $newAmount - $this->user->getAmount();
            $this->user->setAmount($this->user->getAmount() + $different);
        }
    }
}