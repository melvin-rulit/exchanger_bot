<?php

namespace App\Exceptions\User;

use Exception;

class AuthUserNotFoundException extends Exception

{
    protected $message = 'Аутентифицированный пользователь не найден.';
}
