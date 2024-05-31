<?php

namespace App\Transaction;

use App\Trait\EnumToArrayTrait;

enum TransactionEnum: int
{

    use EnumToArrayTrait;

    case Expense = 2;
    case Profit = 1;

    case Transfer = 3;

    public static function transactionTypes(): array
    {
        $types = TransactionEnum::valueAsKey();
        unset($types[TransactionEnum::Transfer->name]);
        return $types;
    }
}
