<?php

namespace App\Service;

use InvalidArgumentException;

class CurrencyConverterService
{
	protected array $exchangesRates = [];
	
	public function __construct(
		protected ExchangeService $exchangeService
	)
	{
		$this->exchangesRates = $this->setRates();
	}
	
	private function setRates(): array
	{
		return $this->exchangesRates = [
			'USD_PLN' => $this->exchangeService->currencyExchange('USD_PLN'),
			'USD_UAH' => $this->exchangeService->currencyExchange('USD_UAH'),
			
			'PLN_USD' => $this->exchangeService->currencyExchange('PLN_USD'),
			'PLN_UAH' => $this->exchangeService->currencyExchange('PLN_UAH'),
			
			'UAH_USD' => $this->exchangeService->currencyExchange('UAH_USD'),
			'UAH_PLN' => $this->exchangeService->currencyExchange('UAH_PLN'),
		];
	}
	
	public function convertAmount(string $from, string $to, float $amount): float
	{
		$rateKey = "{$from}_$to";
		if (!array_key_exists($rateKey, $this->exchangesRates)) {
			throw new InvalidArgumentException("Exchange rate not found for $from to $to");
		}
		$exchangeRate = $this->exchangesRates[$rateKey];
		return $amount * $exchangeRate;
	}
	
	public function getRate(string $from, string $to)
	{
		$rateKey = "{$from}_$to";
		return $this->setRates()[$rateKey];
	}
	
	
}