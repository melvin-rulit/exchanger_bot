<?php

namespace App\Validators;

use Validator;
use App\DTO\CallbackTelegramData;

class AmountValidator
{
    public static function isValid(CallbackTelegramData $callback): bool
    {
        if (!empty($callback->photos)) {
            return false;
        }

        $validator = Validator::make(['amount' => $callback->text],
            [
                'amount' => ['required', 'numeric', 'min:1'],
            ]
        );

        return !$validator->fails();
    }
}
