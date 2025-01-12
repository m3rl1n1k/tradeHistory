<?php

namespace App\Service\Transaction;

use App\Entity\Transaction;

interface CalculationInterface
{
    public function calculate(string $flag, ?Transaction $transaction = null, array $options = []): void;
}