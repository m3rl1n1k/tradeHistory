<?php

namespace App\Transaction;

use App\Entity\User;
use App\Transaction\Entity\Transaction;
use Doctrine\ORM\Query;
use Symfony\Component\Security\Core\User\UserInterface;

interface TransactionInterface
{
	/**
	 * @param UserInterface|User $user
	 * @param bool $is_array
	 * @return array|Query
	 */
	public function getTransactionListByUser(UserInterface|User $user, bool $is_array = true)
	:array|Query;
	
	/**
	 * @param UserInterface|User $user
	 * @param Transaction $transaction
	 * @param float $oldAmount
	 */
    public function calculateAmount(UserInterface|User $user, Transaction $transaction, float $oldAmount = 0):void;
	
	/**
	 * @param int $id
	 * @return array
	 */
    public function getTransactionById(int $id): array;
	
	/**
	 * @param UserInterface|User $user
	 * @param int $type
	 * @return array
	 */
	public function getTransactionByType(UserInterface|User $user, int $type): array;
	
	/**
	 * @param User $user
	 * @param Transaction $transaction
	 * @return void
	 */
	public function removeTransaction(User $user, Transaction $transaction):void;
}