<?php

namespace App\Enum;

use App\Trait\EnumToArrayTrait;

enum TransactionColumnEnum
{
    use EnumToArrayTrait;

//    case Id;
    case Amount;
//    case Type;
    case Description;
    case Category;
    case Date;
    case Wallet;

    public static function transactionColumns(): array
    {
        return TransactionColumnEnum::associativeArray();
    }
}
