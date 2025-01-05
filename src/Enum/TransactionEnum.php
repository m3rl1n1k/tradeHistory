<?php

namespace App\Enum;

use App\Trait\EnumToArrayTrait;

enum TransactionEnum: int
{

    use EnumToArrayTrait;

    case Expense = 2;
    case Profit = 1;


    public static function transactionTypes(): array
    {
        return TransactionEnum::valueAsKey();
    }
}
