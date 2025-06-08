<?php

namespace App\Actions;

use App\DTO\WalletSelectionData;
use App\Enums\Wallet\WalletField;
use App\Enums\TelegramCallbackAction;
use App\Exceptions\TelegramApiException;
use App\Telegram\Keyboard\KeyboardFactory;
use App\Services\ClientService\ClientsService;
use App\Services\RedisService\RedisSessionService;
use App\Services\TelegramBotService\TelegramMessageService;

class StartWalletAction
{
    public function __construct(protected ClientsService $clientsService, protected TelegramMessageService $telegramMessageService, protected RedisSessionService $redis) {}

    /**
     * @throws TelegramApiException
     */
    public function execute(WalletSelectionData $data): void
    {
        $this->redis->forgetWalletConsultant($data->chatId);
        $this->clientsService->setClientWalletInput($data->clientId);

        $keyboard['inline_keyboard'][] = KeyboardFactory::toConsultation(TelegramCallbackAction::ToConsultation->value. WalletField::WALLET->value);
        $keyboard['inline_keyboard'][] = KeyboardFactory::toCancel(true);

        $this->telegramMessageService->sendMessageWithButtons($data->chatId, __('messages.wait_wallet'), $keyboard);

    }
}
