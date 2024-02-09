<?php

namespace App\Transaction\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Transaction\Entity\Transaction;
use App\Transaction\Enum\TransactionEnum;
use App\Transaction\Repository\TransactionRepository;
use App\Transaction\TransactionInterface;
use Doctrine\ORM\Query;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TransactionService implements TransactionInterface
{
	public function __construct(protected Security              $security,
								protected UserRepository        $userRepository,
								protected TransactionRepository $transactionRepository
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
	
	public function getTransactionsPerPeriod($dateStart, $dateEnd)
	{
		return $this->transactionRepository->getTransactionsPerPeriod($dateStart->getTimestamp(),
			$dateEnd->getTimestamp());
		
	}
	
	/**
	 * @inheritDoc
	 */
	public function getTransactionListByUser(UserInterface|User $user, bool $is_array = false):
	array|Query
	{
		if ($is_array) {
			return $this->transactionRepository->findBy(['user' => $user->getId()]);
		}
		return $this->transactionRepository->getUserTransactionsQuery($user);
	}
	
	/**
	 * @inheritDoc
	 */
	public function calculateAmount( UserInterface|User $user, Transaction $transaction, float $oldAmount = 0): void
	{
		if ($transaction->isIncome()) {
			$user->setAmount($user->incrementAmount($transaction->getAmount()));
		}
		
		if ($transaction->isExpense()) {
			$user->setAmount($user->decrementAmount($transaction->getAmount()));
		}
		$this->isExpenseCurrentMoreOldAmount($oldAmount, $user, $transaction);
		$this->isExpenseOldMoreCurrentAmount($oldAmount, $user, $transaction);
		$this->isExpenseOldEqualCurrentAmount($oldAmount, $user, $transaction);
		$this->isIncome($user, $transaction);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getTransactionById(int $id): Transaction
	{
		return $this->transactionRepository->findBy(['id' => $id]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getTransactionByType(UserInterface|User $user, int $type): array
	{
		return $this->transactionRepository->findBy(
			[
				'type' => $type,
				'user' => $user->getId()
			]
		);
	}
	
	private function isExpenseCurrentMoreOldAmount(float $oldAmount, UserInterface|User $user, Transaction $transaction): void
	{
		if ($transaction->isExpense() && $transaction->getAmount() > $oldAmount) {
			$difference = $transaction->getAmount() - $oldAmount;
			$newAmount = ($difference > 0) ? $user->getAmount() - $difference : $user->getAmount() + abs($difference);
			$user->setAmount($newAmount);
		}
	}
	
	private function isExpenseOldMoreCurrentAmount(float $oldAmount, UserInterface|User $user, Transaction $transaction): void
	{
		if ($transaction->isExpense() && $transaction->getAmount() < $oldAmount) {
			$amount = $user->getAmount() + ($oldAmount - $transaction->getAmount());
			$user->setAmount($amount);
		}
	}
	
	private function isExpenseOldEqualCurrentAmount(float $oldAmount, UserInterface|User $user, Transaction $transaction): void
	{
		if ($transaction->isExpense() && $transaction->getAmount() === $oldAmount) {
			$user->setAmount($user->decrementAmount($transaction->getAmount()));
		}
	}
	
	private function isIncome(UserInterface|User $user, Transaction $transaction): void
	{
		if ($transaction->isIncome()) {
			
			$sumIncome = $this->summaryTransactions($this->getTransactionByType($user, TransactionEnum::INCOME));
			$sumExpense = $this->summaryTransactions($this->getTransactionByType($user, TransactionEnum::EXPENSE));
			
			$user->setAmount($sumIncome - $sumExpense);
		}
	}
	
}