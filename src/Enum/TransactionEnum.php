<?php

namespace App\Enum;

use App\Trait\EnumToArrayTrait;

enum TransactionEnum: int
{
	
	use EnumToArrayTrait;
    case Income = 1;
    case Expense = 2;
//    case Transaction = 3;
}
