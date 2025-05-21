<?php

namespace App\Exceptions\User;

use Exception;

class UsersNotFoundException extends Exception

{
    protected $message = 'Пользователи не найдены';
}
