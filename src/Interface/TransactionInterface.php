<?php

namespace App\Interface;

use App\Entity\Transaction;
use App\Entity\User;
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
     * @param int $type
     * @param UserInterface|User $user
     * @param Transaction $transaction
     * @return array
     */
    public function getAllIncome(int $type, UserInterface|User $user, Transaction $transaction): array;

    /**
     * @param int $type
     * @param UserInterface|User $user
     * @param Transaction $transaction
     * @return array
     */
    public function getAllExpense(int $type, UserInterface|User $user, Transaction $transaction): array;

    /**
     * @param array $transactions
     * @return float
     */
    public function summaryTransactions(array $transactions):float;

    /**
     * @param float $amount
     * @return float
     */
    public function increment(float $amount):float;

    /**
     * @param float $amount
     * @return float
     */
    public function decrement(float $amount):float;
}