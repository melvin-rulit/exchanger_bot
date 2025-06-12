<?php

namespace App\Handlers\Callback\GoBack;

use Exception;
use App\Handlers\Callback\GoToMainHandler;
use App\Services\ClientService\ClientsService;
use App\Services\RedisService\RedisSessionService;
use App\Services\TelegramBotService\TelegramMessageService;

class CountryBackHandler
{
    public function __construct(protected TelegramMessageService $telegramMessageService, protected  ClientsService $clientsService, protected RedisSessionService $redisSessionService) {}

    /**
     * @throws Exception
     */
    public function handle(int $clientBotId, int|string $chatId, int $messageId, ?string $action = null,): void
    {
        $handler = app(GoToMainHandler::class);

        $handler->handle($clientBotId, $chatId, $messageId, $action);
    }
}
