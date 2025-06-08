<?php

namespace App\Actions;

use App\DTO\RequisiteSelectionData;
use App\Enums\TelegramCallbackAction;
use App\Enums\Requisite\RequisiteField;
use App\Exceptions\TelegramApiException;
use App\Telegram\Keyboard\KeyboardFactory;
use App\Services\ClientService\ClientsService;
use App\Services\RedisService\RedisSessionService;
use App\Services\TelegramBotService\TelegramMessageService;

class StartRequisiteAction
{
    public function __construct(protected ClientsService $clientsService, protected TelegramMessageService $telegramMessageService, protected RedisSessionService $redis) {}

    /**
     * @throws TelegramApiException
     */
    public function execute(RequisiteSelectionData $data): void
    {
        $this->redis->forgetRequisiteConsultant($data->chatId);
        $this->clientsService->setRequisiteInput($data->clientBotId);

        $keyboard['inline_keyboard'][] = KeyboardFactory::toConsultation(TelegramCallbackAction::ToConsultation->value. RequisiteField::REQUISITE->value);
        $keyboard['inline_keyboard'][] = KeyboardFactory::toCancel(true);
        $this->telegramMessageService->sendMessageWithButtons($data->chatId, __('messages.wait_requisite'), $keyboard, $data->messageId);

    }
}
