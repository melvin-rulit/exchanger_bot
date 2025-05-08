<?php

use App\Exceptions\Helpers\InvalidStringValueException;

if (!function_exists('ensure_string')) {
    /**
     * @throws InvalidStringValueException
     */
    function ensure_string(mixed $value, string $key = ''): string
    {
        if (!is_string($value) || trim($value) === '') {
            $message = $key ? "Config [{$key}] must be a non-empty string." : 'Value must be a non-empty string.';
            throw new InvalidStringValueException($message);
        }

        return $value;
    }
}
