<?php

namespace App\Enum;

use App\Trait\EnumToArrayTrait;

enum TransactionEnum: int
{
	
	use EnumToArrayTrait;
	
	case Expense = 2;
	case Income = 1;
	
	case Transaction = 3;
}
