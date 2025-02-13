<?php

namespace App\Helper;

class StringHelper
{
    public static function uc_first(string $string, $encoding = 'UTF-8'): string
    {
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $rest = mb_substr($string, 1, null, $encoding);
        return mb_strtoupper($firstChar, $encoding) . $rest;
    }
}