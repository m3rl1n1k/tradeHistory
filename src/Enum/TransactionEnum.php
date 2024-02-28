<?php

namespace App\Enum;

use App\Trait\EnumToArrayTrait;

enum TransactionEnum
{
	
	use EnumToArrayTrait;
    const INCOME = 1;
    const EXPENSE = 2;
    const TRANSACTION = 3;
}
