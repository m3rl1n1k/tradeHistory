<?php

namespace App\Transaction;

use App\Entity\Transaction;
use App\Entity\Wallet;
use Doctrine\ORM\Query;

interface TransactionServiceInterface
{
	public function getTransactions(bool $is_array = false): array|Query;
	
	public function getTransactionsPerPeriod($dateStart, $dateEnd): array;
	
	public function calculate(Wallet $wallet, Transaction $transaction, float $oldAmount = 0, bool $newTransaction =
	false): void;
}