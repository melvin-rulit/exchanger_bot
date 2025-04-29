<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    public function getOrderIdFromRoute(string $key = 'orderId'): int
    {
        return (int) $this->route($key);
    }
}
