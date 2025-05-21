<?php

namespace App\Exceptions\User\Chat;

use Exception;

class PinnedChatNotFoundException extends Exception

{
    protected $message = 'Закреплённый чата не найден.';
}
