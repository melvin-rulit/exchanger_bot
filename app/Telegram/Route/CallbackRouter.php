<?php

namespace App\Telegram\Route;

use App\DTO\CallbackData;
use App\Enums\TelegramCallbackAction;
use App\Handlers\Callback\BankCallbackHandler;
use App\Handlers\Callback\CountryCallbackHandler;
use App\Handlers\Callback\CurrencyCallbackHandler;
use App\Handlers\Callback\GoBack\BankBackHandler;
use App\Handlers\Callback\GoBack\CountryBackHandler;
use App\Handlers\Callback\GoBack\CurrencyBackHandler;
use App\Handlers\Callback\GoToMainHandler;
use App\Handlers\Callback\LanguageCallbackHandler;
use Illuminate\Support\Facades\Log;

class CallbackRouter
{
    public function __construct(
        protected LanguageCallbackHandler $languageCallbackHandler,
        protected GoToMainHandler $goToMainHandler,
        protected CountryCallbackHandler $countryCallbackHandler,
        protected CountryBackHandler $countryBackHandler,
        protected BankCallbackHandler $bankCallbackHandler,
        protected BankBackHandler $bankBackHandler,
        protected CurrencyBackHandler $currencyBackHandler,
        protected CurrencyCallbackHandler $currencyCallbackHandler,

    ) {}

    public function route(CallbackData $callbackData,int $clientBotId,int $chatId,int $messageId): void {

        match ($callbackData->action) {

            TelegramCallbackAction::SelectLanguage->value => $this->languageCallbackHandler->handle($callbackData->payload['language_code'], $clientBotId, $chatId, $messageId),
            TelegramCallbackAction::SelectCountry->value => $this->countryCallbackHandler->handle($callbackData->payload['country_code'], $clientBotId, $chatId, $messageId),
            TelegramCallbackAction::SelectBank->value, => $this->bankCallbackHandler->handle($callbackData->payload['bank_id'], $clientBotId, $chatId, $messageId),
            TelegramCallbackAction::SelectCurrency->value, => $this->currencyCallbackHandler->handle($callbackData->payload['currency_id'], $clientBotId, $chatId, $messageId),

            TelegramCallbackAction::ToMain->value => $this->goToMainHandler->handle($clientBotId, $chatId, $messageId),

            TelegramCallbackAction::SelectCountryBack->value => $this->countryBackHandler->handle($clientBotId, $chatId, $messageId),
            TelegramCallbackAction::SelectBankBack->value => $this->bankBackHandler->handle($clientBotId, $chatId, $messageId),
            TelegramCallbackAction::SelectCurrencyBack->value => $this->currencyBackHandler->handle($clientBotId, $chatId, $messageId),


            default => Log::warning('CallbackRouter: неизвестное действие', [
                'action' => $callbackData->action,
                'payload' => $callbackData->payload,
                'chat_id' => $chatId,
            ]),

        };
    }

}
