<?php

namespace App\Exceptions\User;

use Exception;

class UserNotFoundException extends Exception

{
    protected $message = 'Пользователь с таким id не найден.';
}
