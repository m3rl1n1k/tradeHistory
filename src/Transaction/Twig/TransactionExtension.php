<?php

namespace App\Transaction\Twig;

use App\Transaction\Enum\TransactionEnum;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TransactionExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('type', [$this, 'numberToName']),
        ];
    }

    public function numberToName(int $type): string
    {
        $types = [
            TransactionEnum::INCOME => "Income",
            TransactionEnum::EXPENSE => "Expense",
            TransactionEnum::TRANSACTION => "Transaction",
        ];
        return $types[$type];
    }
}