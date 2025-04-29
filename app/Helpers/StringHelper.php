<?php

if (!function_exists('ensure_string')) {
    function ensure_string(mixed $value): string
    {
        if (!is_string($value)) {
            throw new \UnexpectedValueException("'{$value}' must return string.");
        }

        return $value;
    }
}
