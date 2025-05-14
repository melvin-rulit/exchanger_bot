<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    public function getIdFromRoute(string $key): int
    {
        $value = $this->route($key);

        if (!is_numeric($value)) {
            throw new \InvalidArgumentException("Параметр из маршрута '{$key}' отсутствует или не является действительным числовым значением.");
        }
        return (int) $value;
    }

}
