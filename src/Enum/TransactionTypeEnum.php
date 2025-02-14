<?php

namespace App\Enum;

use App\Trait\EnumToArrayTrait;

enum TransactionTypeEnum: int
{

    use EnumToArrayTrait;

    case Expense = 2;
    case Profit = 1;

    case Transfer = 3;


    public static function transactionTypes(): array
    {
        $types = TransactionTypeEnum::valueAsKey();
        return array_slice($types, 0, 2);
    }
}
