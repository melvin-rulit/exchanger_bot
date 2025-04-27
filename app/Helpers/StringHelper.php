<?php

if (!function_exists('ensure_string')) {
    function ensure_string(string $key): string
    {
        $value = config($key);

        if (!is_string($value)) {
            throw new \UnexpectedValueException("'{$key}' must return string.");
        }

        return $value;
    }
}
