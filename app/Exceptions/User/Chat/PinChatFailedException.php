<?php

namespace App\Exceptions\User\Chat;

use Exception;

class PinChatFailedException extends Exception

{
    protected $message = 'Ошибка при выполнении операции c чатом .';
}
