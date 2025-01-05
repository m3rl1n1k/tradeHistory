<?php

namespace App\Service\Transaction;

use App\Entity\Transaction;
use App\Entity\Wallet;

interface CalculationInterface
{
    public function calculate(string $flag, ?Transaction $transaction = null, ?Wallet $wallet = null, array $options = []): void;
}