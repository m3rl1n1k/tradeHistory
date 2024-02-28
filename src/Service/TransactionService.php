<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\Wallet;
use App\Enum\TransactionEnum;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
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
		/**
		 * @function $this->isExpenseOldMoreCurrentAmount()// якщо стара сума більше нової (від старої віднімаєм нову
		 * і різницю на баланс)
		 * @function isExpenseCurrentMoreOldAmount// якщо стара сума менше нової (від старої віднімаєм нову і різницю
		 * знімаєм з балансу баланс)
		 **/
//юзера замінити на відповідний рахунок з яким мають проводитись зміни.  тобто в даний метод передаєм рахунок
		$this->CurrentMoreOldAmount($wallet, $transaction, $oldAmount);
		$this->OldMoreCurrentAmount($wallet, $transaction, $oldAmount);
		if ($transaction->getAmount() === $oldAmount && $transaction->isExpense()) {
			$wallet->setAmount(
				$wallet->getAmount() - $transaction->getAmount()
			);
		}
		if ($transaction->getAmount() === $oldAmount && $transaction->isIncome()) {
			$wallet->setAmount(
				$wallet->getAmount() + $transaction->getAmount()
			);
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
}