<?php

namespace App\Exceptions\LockScreen;

use Exception;

class LockPasswordMismatchException extends Exception

{
    protected $message = 'Пароль не совпадает';
}
