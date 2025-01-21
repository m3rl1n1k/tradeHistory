<?php

namespace App\Trait;

use Othyn\PhpEnumEnhancements\Traits\EnumEnhancements;

trait EnumToArrayTrait
{
    use EnumEnhancements;

    public static function associativeArray(): array
    {
        $array = self::valueArray();
        return array_combine($array, $array);
    }

    public static function valueAsKey(): array
    {
        $values = [];
        foreach (self::cases() as $enum) {
            $values[$enum->name] = $enum->value;
        }
        return $values;
    }


}
