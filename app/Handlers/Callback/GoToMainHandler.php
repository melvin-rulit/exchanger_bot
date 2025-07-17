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
use App\Services\RedisService\RedisMessageService;
use App\Services\TelegramBotService\TelegramMessageService;
use App\Telegram\Keyboard\KeyboardFactory;

class GoToMainHandler
{
    public function __construct(protected ClientsService $clientsService, protected TelegramMessageService $telegramMessageService, protected RedisFieldService $redis, protected OrderService $orderService, protected RedisMessageService $redisMessageService) {}

    /**
     * @throws TelegramApiException
     * @throws OrderNotFoundException
     */
    public function handle(int $clientBotId, int $chatId, int $messageId, string $action): void
    {
        if ($action === TelegramCallbackAction::Cancel->value){
            $order = $this->orderService->changeStatus($this->redis->getOrderId($chatId), ModelEnum::CLOSEORDERSTATUS);
            broadcast(new OrderClosed($order, 'order_closed'));
        }

        $this->clientsService->setClientMainInput($clientBotId, __('buttons.to_main'));

        $keyboard = KeyboardFactory::startKeyboard();

        $this->telegramMessageService->deleteMessages($chatId);
        $greeting = $this->telegramMessageService->sendMessageWithButtons($chatId, __('messages.greeting'), $keyboard, $messageId);

        $this->redisMessageService->setDeletedMessageForChat($chatId, $greeting);
    }
}
