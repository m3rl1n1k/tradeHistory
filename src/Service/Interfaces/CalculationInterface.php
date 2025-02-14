<?php

namespace App\Service\Interfaces;

interface CalculationInterface
{
    public function calculate(string $flag, ?object $object = null, array $options = []): void;
}