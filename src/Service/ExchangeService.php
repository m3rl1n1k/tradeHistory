<?php

namespace App\Service;

use Quandl;

class ExchangeService
{
	public function currencyExchange(string $currency): float
	{
		
		$currencyPrice = 0;
		$currency = str_replace('_', '', $currency);
		$api_key = "eRVGMTQwnLrBZJUprzDC";
		$quandl = new Quandl($api_key);
		$currencies = $quandl->getSymbol("CURRFX/$currency", [
			"sort_order" => "desc",
			"rows" => 1,
		]);
		if ($currencies){
			foreach ($currencies as $currency) {
				$array = array_shift($currency->data);
				$currencyPrice = next($array);
			}
		}
		return round($currencyPrice, 4, PHP_ROUND_HALF_DOWN);
	}
}