<?php

namespace App\Transaction;

use App\Trait\EnumToArrayTrait;

enum TransactionEnum: int
{
	
	use EnumToArrayTrait;
	
	case Expense = 2;
	case Income = 1;
	
	case Transfer = 3;
}
