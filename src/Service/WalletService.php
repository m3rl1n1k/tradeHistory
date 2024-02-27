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
	
	public function currencyExchange(User $user, string $currency): array
	{
		
		$currencyPrice = [];
		$mainCurrency = $user->getCurrency();
		$api_key = "eRVGMTQwnLrBZJUprzDC";
		$quandl = new Quandl($api_key);
		$currencies = $quandl->getSymbol("CURRFX/$mainCurrency.$currency", [
			"sort_order" => "desc",
			"rows" => 1,
		]);
		if ($currencies){
			foreach ($currencies as $currency) {
				$array = array_shift($currency->data);
				$currencyPrice[$currency->dataset_code] = next($array);
			}
		}
		return $currencyPrice;
	}
}