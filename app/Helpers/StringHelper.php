<?php

use App\Exceptions\Helpers\InvalidStringValueException;

if (!function_exists('ensure_string')) {
    /**
     * @throws InvalidStringValueException
     */
    function ensure_string(mixed $value, string $key = ''): string
    {
        if (!is_string($value) || trim($value) === '') {
            $message = $key ? "Конфигурация [{$key}] должна быть установленна в config или в env." : 'Значение должно быть непустой строкой.';
            throw new InvalidStringValueException($message);
        }

        return $value;
    }
}
