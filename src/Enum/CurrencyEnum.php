<?php

namespace App\Enum;



use App\Trait\EnumToArrayTrait;

enum CurrencyEnum: string
{
	use EnumToArrayTrait;
	
	case USD = 'USD';
	case EUR = 'EUR';
	case UAH = 'UAH';
	case PLN = 'PLN';
	
}
