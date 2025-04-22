<?php

if (!function_exists('config_string')) {
    function config_string(string $key): string
    {
        $value = config($key);

        if (!is_string($value)) {
            throw new \UnexpectedValueException("Config key '{$key}' must return string.");
        }

        return $value;
    }
}
