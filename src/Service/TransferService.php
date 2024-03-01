<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\Transfer;
use App\Entity\User;
use App\Entity\Wallet;
use App\Enum\TransactionEnum;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;

class TransferService
{
	public function __construct()
	{
	}
	
	public function calculate(EntityManagerInterface $em, Transfer $transfer, $user): void
	{
		$expenseWallet = $transfer->getFromWallet();
		$incomeWallet = $transfer->getToWallet();
		if ($expenseWallet->getNumber() === $incomeWallet->getNumber()) {
			throw new LogicException('Can\'t make transfer to same wallet!');
		}
		if ($expenseWallet->getAmount() === 0) {
			throw new LogicException('Can\'t make transfer from empty wallet!');
		}
		if ($expenseWallet->getAmount() < $transfer->getAmount()) {
			throw new LogicException('Enter same ro less amount, which you have on wallet!');
		}
		
		
		if ($incomeWallet->getCurrency() !== $expenseWallet->getCurrency()) {
			$expenseWallet->setAmount($expenseWallet->decrement($transfer->getAmount()));
			switch ($incomeWallet->getCurrency() !== $expenseWallet->getCurrency()) {
				case $incomeWallet->getCurrency() === "USD":
					$amount = $transfer->getAmount() / 4;
					$incomeWallet->setAmount($incomeWallet->increment($amount));
					$this->newTransaction($em,$incomeWallet, $amount, $user);
					break;
				case $incomeWallet->getCurrency() === "PLN":
					$amount = $transfer->getAmount() * 4;
					$incomeWallet->setAmount($incomeWallet->increment($amount));
					break;
				case $incomeWallet->getCurrency() === "UAH":
					$amount = $transfer->getAmount() * 9;
					$incomeWallet->setAmount($incomeWallet->increment($amount));
					break;
			}
		} else {
			$expenseWallet->setAmount($expenseWallet->decrement($transfer->getAmount()));
			$incomeWallet->setAmount($incomeWallet->increment($transfer->getAmount()));
		}
		
		
	}
	
	protected function newTransaction(EntityManagerInterface $em, Wallet $wallet, int $amount, User $user): void
	{
		$date = new DateTime('now');
		$transaction = new Transaction();
		$transaction->setAmount($amount)
			->setWallet($wallet)
			->setType(TransactionEnum::Income->value)
			->setDate($date)
			->setUserId($user);
		$em->persist($transaction);
		$em->flush();
	}
}