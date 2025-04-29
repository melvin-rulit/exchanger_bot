<?php

namespace App\Services\Web;

use App\Services\ClientService\ClientsService;
use App\Services\RedisSessionService;
use App\Services\TelegramBotService\TelegramMessageService;

abstract class BaseWebService
{
    public function __construct(protected RedisSessionService $redis, protected TelegramMessageService $telegramMessageService, protected ClientsService $clientsService){}

    public function findOrThrow($modelClass, $id, $exceptionClass, $message = null)
    {
        $model = $modelClass::find($id);

        if (!$model) {
            $message = $message ?? "{$modelClass} с ID {$id} не найден.";
            throw new $exceptionClass($message);
        }

        return $model;
    }
}
