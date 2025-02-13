<?php

namespace App\Enum;

use App\Trait\EnumToArrayTrait;

enum TransactionTypeEnum: int
{

    use EnumToArrayTrait;

    case Expense = 2;
    case Profit = 1;


    public static function transactionTypes(): array
    {
        return TransactionTypeEnum::valueAsKey();
    }
}
