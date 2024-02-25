<?php

namespace App\Wallet\Enum;

use Othyn\PhpEnumEnhancements\Traits\EnumEnhancements;

enum CurrencyEnum: string
{
	use EnumEnhancements;
	
	case USD = 'USD';
	case EUR = 'EUR';
	case UAH = 'UAH';
	case PLN = 'PLN';
	
	public static function associativeArray(): array
	{
		return array_combine(self::valueArray(), self::valueArray());
	}
}
