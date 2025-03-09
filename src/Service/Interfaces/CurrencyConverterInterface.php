<?php

namespace App\Service\Interfaces;

interface CurrencyConverterInterface
{
    public function convert(float $amount, string $currencyFrom, string $currencyIn);

    public function getRate();
}