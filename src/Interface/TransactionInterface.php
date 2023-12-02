<?php

namespace App\Interface;

use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\TransactionEnum;
use Symfony\Component\Security\Core\User\UserInterface;

interface TransactionInterface
{
    public function amount(UserInterface|User $user, Transaction $transaction):void;

    /**
     * @param float $oldAmount
     * @param UserInterface|User $user
     * @param Transaction $transaction
     */
    public function editAmount(float $oldAmount, UserInterface|User $user, Transaction $transaction):void;

    /**
     * @param UserInterface|User $user
     * @param int $type
     * @return array
     */
    public function getByType(UserInterface|User $user, int $type): array;

    public function summaryTransactions(array $transactions):float;
}