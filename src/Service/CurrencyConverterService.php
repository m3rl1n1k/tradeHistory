<?php

namespace App\Service;

use App\Entity\ExchangeRate;
use App\Service\Interfaces\CurrencyConverterInterface;
use Psr\Cache\InvalidArgumentException;

class CurrencyConverterService implements CurrencyConverterInterface
{
    private $currencyRate;

    public function __construct(protected ExchangeRateService $exchangeService)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function convert(float $amount, string $currencyFrom, string $currencyIn): float
    {
        if ($currencyIn === "UAH" || $currencyFrom === "UAH") {
            return $amount;
        }
        if ($currencyFrom === $currencyIn) {
            return $amount;
        }
        $exchangeRate = $this->exchangeService->getExchangeRate($currencyIn, $currencyFrom);
        if ($exchangeRate instanceof ExchangeRate) {
            $exchangeRate = $exchangeRate->getCurrencyRate();
        }
        $this->currencyRate = $exchangeRate[$currencyFrom];
        $result = $amount / $exchangeRate[$currencyFrom];
        return round($result, 2, PHP_ROUND_HALF_DOWN);
    }

    public function getRate()
    {
        return $this->currencyRate;
    }
}