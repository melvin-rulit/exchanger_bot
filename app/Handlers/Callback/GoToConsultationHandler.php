<?php

namespace App\Handlers\Callback;

use App\Enums\Bank\BankField;
use App\Enums\Amount\AmountField;
use App\Enums\Wallet\WalletField;
use App\Enums\Country\CountryField;
use App\Enums\Requisite\RequisiteField;
use App\Exceptions\TelegramApiException;
use App\Telegram\Keyboard\KeyboardFactory;
use App\Services\ClientService\ClientsService;
use App\Services\RedisService\RedisSessionService;
use App\Services\TelegramBotService\TelegramMessageService;
use Illuminate\Support\Facades\Log;

class GoToConsultationHandler
{
    public function __construct(protected ClientsService $clientsService, protected TelegramMessageService $telegramMessageService, protected RedisSessionService $redis) {}

    /**
     * @throws TelegramApiException
     */
    public function handle(string $consultationType, int $clientBotId, int $chatId, int $messageId): void
    {
        if ($consultationType === CountryField::COUNTRY->value)
        {
            $this->redis->setCountryConsultant($chatId, 0);
        }
        if ($consultationType === BankField::BANK->value)
        {
            $this->redis->setBankConsultant($chatId, 0);
        }
        if ($consultationType === AmountField::AMOUNT->value)
        {
            $this->redis->setAmountConsultant($chatId, 0);
        }
        if ($consultationType === RequisiteField::REQUISITE->value)
        {
            $this->redis->setRequisiteConsultant($chatId, 0);
        }
        if ($consultationType === WalletField::WALLET->value)
        {
            $this->redis->setWalletConsultant($chatId, 0);
        }

        $keyboard = KeyboardFactory::toBackMenu();

        if (!RequisiteField::REQUISITE->value){
            $this->telegramMessageService->deleteMessage($chatId, $messageId);
        }

        $this->telegramMessageService->sendMessageWithButtons($chatId, __('messages.consultation'), $keyboard, $messageId);
    }
}
