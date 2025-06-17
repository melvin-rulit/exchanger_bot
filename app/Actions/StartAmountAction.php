<?php

namespace App\Actions;

use App\DTO\AmountSelectionData;
use App\Enums\Amount\AmountField;
use App\Enums\TelegramCallbackAction;
use App\Exceptions\TelegramApiException;
use App\Telegram\Keyboard\KeyboardFactory;
use App\Services\ClientService\ClientsService;
use App\Services\RedisService\RedisSessionService;
use App\Services\TelegramBotService\TelegramMessageService;

class StartAmountAction
{
    public function __construct(protected ClientsService $clientsService, protected TelegramMessageService $telegramMessageService, protected RedisSessionService $redis) {}

    /**
     * @throws TelegramApiException
     */
    public function execute(AmountSelectionData $data): void
    {
        $this->redis->setBankOrder($data->chatId, $data->bankId);
        $this->redis->forgetAmountConsultant($data->chatId);
        $this->clientsService->setClientAmountInput($data->clientBotId);

        $keyboard['inline_keyboard'][] = KeyboardFactory::toConsultation(TelegramCallbackAction::ToConsultation->value. AmountField::AMOUNT->value);
        $keyboard['inline_keyboard'][] = KeyboardFactory::toBack(TelegramCallbackAction::SelectAmountBack->value);

        $this->telegramMessageService->deleteMessage($data->chatId, $data->messageId);
        $this->telegramMessageService->sendMessageWithButtons($data->chatId, __('messages.enter_the_amount_only_numbers'), $keyboard, $data->messageId);
    }
}
