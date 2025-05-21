<?php

namespace App\Exceptions\User\Chat;

use Exception;

class PinnedChatsNotFoundException extends Exception

{
    protected $message = 'Закреплённые чаты за сегодня не найдены.';
}
