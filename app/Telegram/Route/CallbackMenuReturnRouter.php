<?php

namespace App\Telegram\Route;

use App\Enums\MenuLevelStatus;
use App\DTO\CallbackReturnMenu;
use Illuminate\Support\Facades\Log;
use App\Exceptions\TelegramApiException;
use App\Handlers\RequisiteCallbackHandler;
use App\Handlers\Callback\BankCallbackHandler;
use App\Handlers\Callback\WalletCallbackHandler;
use App\Handlers\Callback\AmountCallbackHandler;
use App\Handlers\Callback\CountryCallbackHandler;
use App\Services\RedisService\RedisSessionService;
use App\Exceptions\Country\CountryNotFoundException;
use App\Exceptions\Country\CountryBankNotFoundException;

class CallbackMenuReturnRouter
{
    public function __construct(
        protected RedisSessionService $redis,
        protected CountryCallbackHandler $countryCallbackHandler,
        protected BankCallbackHandler $bankCallbackHandler,
        protected AmountCallbackHandler $amountCallbackHandler,
        protected RequisiteCallbackHandler $requisiteCallbackHandler,
        protected WalletCallbackHandler $walletCallbackHandler){}

    /**
     * @throws TelegramApiException
     * @throws CountryNotFoundException
     * @throws CountryBankNotFoundException
     */
    public function route(CallbackReturnMenu $callbackData, int $clientBotId, int $chatId, int $messageId): void
    {
        switch (true) {
            case $callbackData->clientStatus === MenuLevelStatus::Country->value:
                $this->countryCallbackHandler->handle($clientBotId, $chatId, $messageId);
                break;

            case $callbackData->clientStatus === MenuLevelStatus::Bank->value:
                $this->bankCallbackHandler->handle($this->redis->getCountryCode($chatId), $clientBotId, $chatId, $messageId);
                break;

            case $callbackData->clientStatus === MenuLevelStatus::Amount->value:
                $this->amountCallbackHandler->handle(1, $clientBotId, $chatId, $messageId);
                break;

            case $callbackData->clientStatus === MenuLevelStatus::Requisite->value:
                $this->requisiteCallbackHandler->handle($clientBotId, $chatId, $messageId);
                break;

            case $callbackData->clientStatus === MenuLevelStatus::Wallet->value:
                $this->walletCallbackHandler->handle($clientBotId, $chatId, $messageId);
                break;

            default:
                Log::warning('callbackReturnMenuKey: неизвестное действие', [
                    'action' => $callbackData->action,
                ]);
                break;
        }
    }
}
