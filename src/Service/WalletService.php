<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Wallet;
use App\Repository\WalletRepository;
use Quandl;

class WalletService
{
	public function __construct(protected WalletRepository $walletRepository)
	{
	}
	
	public function editWallet(Wallet $wallet, string $currency): string
	{
		$number = $wallet->getNumber();
		return $currency . substr($number, 3);
	}
	
	

}