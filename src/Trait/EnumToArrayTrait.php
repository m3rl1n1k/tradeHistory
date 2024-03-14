<?php

namespace App\Trait;

use App\Enum\TransactionEnum;
use Othyn\PhpEnumEnhancements\Traits\EnumEnhancements;

trait EnumToArrayTrait
{
	use EnumEnhancements;
	
	public static function associativeArray(): array
	{
		return array_combine(self::valueArray(), self::valueArray());
	}
	
	public static function valueAsKey(): array
	{
		$values = [];
		foreach (self::cases() as $enum) {
			$values[$enum->name] = $enum->value;
		}
		return $values;
	}
	
	public static function transactionTypes(): array
	{
		$types = TransactionEnum::valueAsKey();
		unset($types[TransactionEnum::Transfer->name]);
		return $types;
	}
}
