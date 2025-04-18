<?php

namespace App\Helpers;

class StringHelper
{
    public static function generateRandomDigits($length = 5): string
    {
        $characters = '0123456789';
        return substr(str_shuffle(str_repeat($characters, $length)), 0, $length);
    }
}
