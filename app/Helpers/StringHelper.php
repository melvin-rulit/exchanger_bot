<?php

namespace App\Helpers;

class StringHelper
{
    public static function generateRandomDigits($length = 5): int
    {
        $characters = '0123456789';
        $number = substr(str_shuffle(str_repeat($characters, $length)), 0, $length);

        return (int) $number;
    }
}
