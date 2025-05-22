<?php

namespace App\Exceptions\Consultation;

use Exception;

class MessageNotFoundException extends Exception

{
    protected $message = 'Сообщения с таким id не найдено.';
}
