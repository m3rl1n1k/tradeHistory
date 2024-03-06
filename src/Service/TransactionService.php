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
	
	private function summaryTransactions(array $transactions): float
	{
		$summary = 0;
		foreach ($transactions as $transaction) {
			$summary = $summary + $transaction->getAmount();
		}
		return $summary;
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
	
	public function calculate(Wallet $wallet, Transaction $transaction, float $oldAmount = 0): void
	{
		
		$this->CurrentMoreOldAmount($wallet, $transaction, $oldAmount);
		$this->OldMoreCurrentAmount($wallet, $transaction, $oldAmount);
		if ($transaction->getAmount() === $oldAmount && $transaction->isExpense()) {
			$wallet->setAmount($wallet->getAmount());
		}
		if ($transaction->getAmount() === $oldAmount && $transaction->isIncome()) {
			$wallet->setAmount($wallet->getAmount());
		}
		
		
	}
	
	public function getTransactionById(int $id): array
	{
		return $this->transactionRepository->findBy(['id' => $id]);
	}
	
	public function getTransactionByType(UserInterface|User $user, int $type): array
	{
		return $this->transactionRepository->findBy(
			[
				'type' => $type,
				'user' => $user->getId()
			]
		);
	}
	
	private function CurrentMoreOldAmount(Wallet $wallet, Transaction $transaction, float $oldAmount): void
	{
		if ($transaction->isExpense() && $transaction->getAmount() > $oldAmount) {
			$difference = $transaction->getAmount() - $oldAmount;
			$newAmount = ($difference > 0) ? $wallet->getAmount() - $difference : $wallet->getAmount() + abs
				($difference);
			$wallet->setAmount($newAmount);
		}
		if ($transaction->isIncome() && $transaction->getAmount() > $oldAmount) {
			$wallet->setAmount(
				$wallet->getAmount() + abs($transaction->getAmount() - $oldAmount)
			);
		}
	}
	
	private function OldMoreCurrentAmount(Wallet $wallet, Transaction $transaction, float $oldAmount,): void
	{
		if ($transaction->isExpense() && $transaction->getAmount() < $oldAmount) {
			$amount = $wallet->getAmount() + ($oldAmount - $transaction->getAmount());
			$wallet->setAmount($amount);
		}
		if ($transaction->isIncome() && $transaction->getAmount() < $oldAmount) {
			$wallet->setAmount(
				$wallet->getAmount() - abs($transaction->getAmount() - $oldAmount)
			);
		}
	}
	
	public function setAmount(Wallet $wallet, Transaction $transaction): void
	{
		if ($transaction->isIncome()) {
			$wallet->setAmount($wallet->increment($transaction->getAmount()));
		}
		
		if ($transaction->isExpense()) {
			$wallet->setAmount($wallet->decrement($transaction->getAmount()));
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
	
	public function newTransaction(EntityManagerInterface $em, Wallet $wallet, int $amount, User $user, array $options): void
	{
		$msg = 'Transfer from %s with exchange rate: %s';
		$msg = sprintf($msg, $wallet->getNumber(), $options['rate']);
		$date = new DateTime('now');
		
		$transaction = new Transaction();
		$transaction
			->setAmount($amount)
			->setWallet($wallet)
			->setType(TransactionEnum::Income->value)
			->setDate($date)
			->setType(TransactionEnum::Transaction->value)
			->setDescription($msg)
			->setUserId($user);
		
		$em->persist($transaction);
	}
}