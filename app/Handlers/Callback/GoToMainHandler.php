<?php

namespace App\Handlers\Callback;

use App\Enums\ModelEnum;
use App\Enums\TelegramCallbackAction;
use App\Events\Order\OrderClosed;
use App\Exceptions\Order\OrderNotFoundException;
use App\Exceptions\TelegramApiException;
use App\Services\ClientService\ClientsService;
use App\Services\OrderService\OrderService;
use App\Services\RedisService\RedisFieldService;
use App\Services\TelegramBotService\TelegramMessageService;
use App\Telegram\Keyboard\KeyboardFactory;

class GoToMainHandler
{
    public function __construct(protected ClientsService $clientsService, protected TelegramMessageService $telegramMessageService, protected RedisFieldService $redis, protected OrderService $orderService) {}

    /**
     * @throws TelegramApiException
     * @throws OrderNotFoundException
     */
    public function handle(int $clientBotId, int $chatId, int $messageId, string $action): void
    {
        $order = $this->orderService->changeStatus($this->redis->getOrderId($chatId), ModelEnum::CLOSEORDERSTATUS);

        if ($action === TelegramCallbackAction::Cancel->value){
            broadcast(new OrderClosed($order, 'order_closed'));
        }

        $this->clientsService->setClientMainInput($clientBotId, __('buttons.to_main'));

        $keyboard = KeyboardFactory::startKeyboard();

        $this->telegramMessageService->deleteMessage($chatId, $messageId);
        $this->telegramMessageService->sendMessageWithButtons($chatId, __('messages.greeting'), $keyboard, $messageId);
    }
}
