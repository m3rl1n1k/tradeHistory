<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\Wallet;
use App\Enum\TransactionEnum;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TransactionService
{
	public function __construct(protected Security              $security,
								protected UserRepository        $userRepository,
								protected TransactionRepository $transactionRepository,
	)
	{
	}
	
	public function getTransactionsPerPeriod(User $user, $dateStart, $dateEnd):
	array|float|int|string
	{
		return $this->transactionRepository->getTransactionsPerPeriod($user, $dateStart, $dateEnd);
	}
	
	public function getTransactionsForUser(UserInterface|User $user, bool $is_array = false):
	array|Query
	{
		if ($is_array) {
			return $this->transactionRepository->findBy(['user' => $user->getId()], ['id' => 'DESC']);
		}
		return $this->transactionRepository->getUserTransactionsQuery($user);
	}
	
	public function calculate(Wallet $wallet, Transaction $transaction, float $oldAmount = 0, bool $new = false): void
	{
		if ($new) {
			if ($transaction->isIncome()) {
				$wallet->setAmount($wallet->increment($transaction->getAmount()));
			}
			if ($transaction->isExpense()) {
				$wallet->setAmount($wallet->decrement($transaction->getAmount()));
			}
		} else {
			$this->CurrentMoreOldAmount($wallet, $transaction, $oldAmount);
			if ($transaction->getAmount() === $oldAmount && $transaction->isExpense()) {
				$wallet->setAmount($wallet->getAmount());
			}
			if ($transaction->getAmount() === $oldAmount && $transaction->isIncome()) {
				$wallet->setAmount($wallet->getAmount());
			}
		}
		
		
	}
	
	private function CurrentMoreOldAmount(Wallet $wallet, Transaction $transaction, float $oldAmount): void
	{
		if ($transaction->isExpense() && $transaction->getAmount() > $oldAmount) {
			$difference = $transaction->getAmount() - $oldAmount;
			$newAmount = ($difference > 0) ? $wallet->getAmount() - $difference : $wallet->getAmount() + abs
				($difference);
			$wallet->setAmount($newAmount);
		} else {
			$amount = $wallet->getAmount() + ($oldAmount - $transaction->getAmount());
			$wallet->setAmount($amount);
		}
		if ($transaction->isIncome() && $transaction->getAmount() > $oldAmount) {
			$wallet->setAmount(
				$wallet->getAmount() + abs($transaction->getAmount() - $oldAmount)
			);
		} else {
			$wallet->setAmount(
				$wallet->getAmount() - abs($transaction->getAmount() - $oldAmount)
			);
		}
	}
	
	public function removeTransaction(Wallet $wallet, Transaction $transaction): void
	{
		$amount = $transaction->getAmount();
		if ($transaction->getType() === TransactionEnum::Expense->value) {
			$amount = $wallet->increment($amount);
		} else {
			$amount = $wallet->decrement($amount);
		}
		$wallet->setAmount($amount);
	}
	
	public function getSum(float|array|int|string $transactions, int $type): float
	{
		$sum = 0;
		foreach ($transactions as $transaction) {
			if ($transaction->getType() === $type) {
				$sum += $transaction->getAmount();
			}
		}
		return $sum;
	}
	
	public function groupTransactionsByCategory(array $transactions): array
	{
		$groupedTransactions = [];
		
		foreach ($transactions as $transaction) {
			$category = $transaction->getCategory();
			if (!$transaction->getCategory()) {
				$groupedTransactions['No category'][] = $transaction;
			} else {
				$groupedTransactions[$category->getName()][] = $transaction;
			}
		}
		
		return $groupedTransactions;
	}
	
	public function newTransaction(EntityManagerInterface $em, Wallet $wallet, int $amount, User $user, array $options = []): void
	{
		$msg = 'Transfer from %s';
		$msg = sprintf($msg, $wallet->getName() ?? $wallet->getNumber());
		
		if (isset($options['rate'])) {
			$msg = 'Transfer from %s with exchange rate: %s';
			$msg = sprintf($msg, $wallet->getNumber(), $options['rate']);
		}
		$date = new DateTime('now');
		
		$transaction = new Transaction();
		$transaction
			->setAmount($amount)
			->setWallet($wallet)
			->setDate($date)
			->setType(TransactionEnum::Transfer->value)
			->setDescription($msg)
			->setUserId($user);
		
		$em->persist($transaction);
	}
}