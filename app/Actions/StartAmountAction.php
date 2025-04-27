<?php

namespace App\Actions;

use App\DTO\AmountSelectionData;
use App\Enums\TelegramCallbackAction;
use App\Exceptions\TelegramApiException;
use App\Services\RedisSessionService;
use App\Services\ClientService\ClientsService;
use App\Services\TelegramBotService\TelegramMessageService;

use App\Telegram\Keyboard\KeyboardFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class StartAmountAction
{
    public function __construct(protected ClientsService $clientsService, protected TelegramMessageService $telegramMessageService, protected RedisSessionService $redis) {}

    /**
     * @throws TelegramApiException
     */
    public function execute(AmountSelectionData $data): void
    {
        $this->clientsService->setClientAmountInput($data->clientBotId);

        $keyboard['inline_keyboard'][] = KeyboardFactory::toBack(TelegramCallbackAction::SelectCountryBack);

        $this->telegramMessageService->deleteMessage($data->chatId, $data->messageId);
        $this->telegramMessageService->sendMessageWithButtons($data->chatId, __('messages.enter_the_amount_only_numbers'), $keyboard, $data->messageId);
//        $this->telegramMessageService->sendMessage($data->chatId, __('messages.enter_the_amount_only_numbers'));
    }
}
