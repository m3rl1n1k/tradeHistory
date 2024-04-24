<?php

namespace App\Enum;

use Othyn\PhpEnumEnhancements\Traits\EnumEnhancements;
use PHPUnit\Util\Color;

enum ColorEnum: string
{

    case PRIMARY = "primary";
    case SECONDARY = "secondary";
    case SUCCESS = "success";
    case DANGER = "danger";
    case WARNING = "warning";
    case INFO = "info";
    case LIGTH = "light";
    case DARK = "dark";

    public static function colors(): array
    {
        $values = [];
        foreach (self::cases() as $enum) {
            $values[$enum->value] = $enum->value;
        }
        return $values;
    }
}
