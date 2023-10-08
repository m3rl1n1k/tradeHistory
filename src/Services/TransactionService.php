<?php

namespace App\Services;

use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\TransactionTypesEnum;
use App\Repository\TransactionRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class TransactionService
{
	public function __construct(
		protected TransactionRepository $transactionRepository
	)
	{
	}

	public function testCalcEdit(UserInterface $user, Transaction $transaction, $oldAmount, $newAmount)
	{
		/**
		 * @var User $user
		 */
		$transaction->setAmount($newAmount);
		if ($newAmount > $oldAmount)
			$user->setAmount($user->getAmount() - ($newAmount - $oldAmount));
		if ($newAmount < $oldAmount)
			$user->setAmount($user->getAmount() + ($oldAmount - $newAmount));
	}

	public function testCalculating(UserInterface $user, Transaction $transaction)
	{
		$amount = null;

		match ($transaction->getType()) {
			TransactionTypesEnum::Expense => $user->setAmount($user->decrementAmount($transaction->getAmount())),
			TransactionTypesEnum::Income => $user->setAmount($user->incrementAmount($transaction->getAmount()))
		};
	}

	public function getOldAmount(Transaction $transaction): ?float
	{
		return $this->transactionRepository->find($transaction->getId())->getAmount();
	}
}