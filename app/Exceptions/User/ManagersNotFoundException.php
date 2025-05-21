<?php

namespace App\Exceptions\User;

use Exception;

class ManagersNotFoundException extends Exception

{
    protected $message = 'Менеджеры не найдены.';
}
