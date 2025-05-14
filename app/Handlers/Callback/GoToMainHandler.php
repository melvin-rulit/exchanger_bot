<?php

namespace App\Handlers\Callback;

use App\Exceptions\TelegramApiException;
use App\Telegram\Keyboard\KeyboardFactory;
use App\Services\OrderService\OrderService;
use App\Services\ClientService\ClientsService;
use App\Exceptions\Order\OrderNotFoundException;
use App\Services\RedisService\RedisFieldService;
use App\Services\TelegramBotService\TelegramMessageService;

class GoToMainHandler
{
    public function __construct(protected ClientsService $clientsService, protected TelegramMessageService $telegramMessageService, protected RedisFieldService $redis, protected OrderService $orderService) {}

    /**
     * @throws TelegramApiException
     * @throws OrderNotFoundException
     */
    public function handle(int $clientBotId, int $chatId, int $messageId): void
    {
        $this->orderService->changeStatus($this->redis->getOrderId($chatId), 'closed');
        $this->clientsService->setClientMainInput($clientBotId, __('buttons.to_main'));

        $keyboard = KeyboardFactory::startKeyboard();

        $this->telegramMessageService->deleteMessage($chatId, $messageId);
        $this->telegramMessageService->sendMessageWithButtons($chatId, __('messages.greeting'), $keyboard, $messageId);
    }
}
