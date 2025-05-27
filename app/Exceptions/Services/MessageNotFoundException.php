<?php

namespace App\Exceptions\Services;

use Exception;

class MessageNotFoundException extends Exception

{
    protected $message = 'Сообщения с таким id не найдено.';
}
