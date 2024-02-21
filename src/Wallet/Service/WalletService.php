<?php

namespace App\Wallet\Service;

use App\Repository\WalletRepository;

class WalletService
{
	public function __construct(protected  WalletRepository $walletRepository)
	{
	}
	
	public function getWallets(int $idUser)
	{
		return $this->walletRepository->findBy(['user' => $idUser]);
	}
}