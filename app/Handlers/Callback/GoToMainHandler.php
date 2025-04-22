<?php

namespace App\Handlers\Callback;

use App\Services\RedisSessionService;
use App\Telegram\Keyboard\KeyboardFactory;
use App\Services\ClientService\ClientsService;
use App\Services\TelegramBotService\TelegramMessageService;

class GoToMainHandler
{
    public function __construct(protected ClientsService $clientsService,protected TelegramMessageService $telegramMessageService, protected RedisSessionService $redisSessionService) {}

    public function handle(int $clientBotId, int $chatId, int $messageId): void {

        $keyboard = KeyboardFactory::startKeyboard();

        $this->telegramMessageService->sendMessageWithButtons($chatId, __('messages.greeting'), $keyboard, $messageId);
    }
}
