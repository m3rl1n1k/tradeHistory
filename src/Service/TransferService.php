<?php

namespace App\Service;

use App\Entity\Transfer;
use LogicException;

class TransferService
{
	public function calculate(Transfer $transfer)
	{
		if ($transfer->getFromWallet()->getNumber() === $transfer->getToWallet()->getNumber()) {
			throw new LogicException('Can\'t make transfer to same wallet!');
		}
		
		$expenseWallet = $transfer->getFromWallet();
		$incomeWallet = $transfer->getToWallet();
		
		$expenseWallet->setAmount($expenseWallet->decrement($transfer->getAmount()));
		$incomeWallet->setAmount($incomeWallet->increment($transfer->getAmount()));
	}
}