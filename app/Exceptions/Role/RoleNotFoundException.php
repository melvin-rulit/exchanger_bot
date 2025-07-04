<?php

namespace App\Exceptions\Role;

use Exception;

class RoleNotFoundException extends Exception

{
    protected $message = 'Ни одной роли не найдено';
}
