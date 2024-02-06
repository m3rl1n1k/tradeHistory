<?php

namespace App\Transaction\Sevice;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Transaction\Entity\Transaction;
use App\Transaction\Enum\TransactionEnum;
use App\Transaction\Repository\TransactionRepository;
use App\Transaction\TransactionInterface;
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
	
	public function amount(UserInterface|User $user, Transaction $transaction): void
	{
		if ($transaction->isIncome()) {
			$user->setAmount($user->incrementAmount($transaction->getAmount()));
		}
		
		if ($transaction->isExpense()) {
			$user->setAmount($user->decrementAmount($transaction->getAmount()));
		}
	}
	
	public function editAmount(float $oldAmount, UserInterface|User $user, Transaction $transaction): void
	{
		if ($transaction->isExpense() && $transaction->getAmount() > $oldAmount) {
			$difference = $transaction->getAmount() - $oldAmount;
			$newAmount = ($difference > 0) ? $user->getAmount() - $difference : $user->getAmount() + abs($difference);
			$user->setAmount($newAmount);
		}
		if ($transaction->isExpense() && $transaction->getAmount() < $oldAmount) {
			$amount = $user->getAmount() + ($oldAmount - $transaction->getAmount());
			$user->setAmount($amount);
		}
		if ($transaction->isExpense() && $transaction->getAmount() === $oldAmount) {
			$user->setAmount($user->decrementAmount($transaction->getAmount()));
		}
		if ($transaction->isIncome()) {
			$user->setAmount(
				$this->summaryTransactions(
					$this->getByType($user, TransactionEnum::INCOME)
				) -
				$this->summaryTransactions($this->getByType($user, TransactionEnum::EXPENSE)
				)
			);
		}
	}
	
	public function getByType(UserInterface|User $user, int $type): array
	{
		return $this->transactionRepository->findBy(
			[
				'type' => $type,
				'user' => $user->getId()
			]
		);
	}
	
	public function summaryTransactions(array $transactions): float
	{
		$summary = 0;
		foreach ($transactions as $transaction) {
			$summary = $summary + $transaction->getAmount();
		}
		return $summary;
	}
	
	public function getTransactionsPerPeriod($dateStart, $dateEnd)
	{
//        dd($date);
		$res = $this->transactionRepository->getTransactionsPerPeriod($dateStart->getTimestamp(), $dateEnd->getTimestamp());
		dd($res);
	}
}