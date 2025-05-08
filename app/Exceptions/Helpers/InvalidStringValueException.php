<?php

namespace App\Exceptions\Helpers;

use Exception;

class InvalidStringValueException extends Exception

{
    public function report(): void
    {
        log_error($this->getMessage());
    }
}
