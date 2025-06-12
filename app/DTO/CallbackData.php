<?php

namespace App\DTO;

use App\Enums\TelegramCallbackAction;

class CallbackData
{
    public function __construct(public string $action, public ?int $clientBotId = null, public array $payload = []){}

    public static function fromTelegram(array $data): self
    {
        if (str_starts_with($data['callbackQuery'], 'language_')) {
            return new self('select_language', $data['clientBotId'], [
                'language_code' => str_replace('language_', '', $data['callbackQuery']),
            ]);
        }
        if (str_starts_with($data['callbackQuery'], 'country_')) {
            return new self(TelegramCallbackAction::SelectBank->value, $data['clientBotId'], [
                'country_code' => str_replace('country_', '', $data['callbackQuery']),
            ]);
        }
        if (str_starts_with($data['callbackQuery'], 'bank_')) {
            return new self(TelegramCallbackAction::InputAmount->value, $data['clientBotId'], [
                'bank_id' => str_replace('bank_', '', $data['callbackQuery']),
            ]);
        }
        if (str_starts_with($data['callbackQuery'], 'currency')) {
            return new self(TelegramCallbackAction::SelectCurrency->value, $data['clientBotId'], [
                'currency_id' => str_replace('currency_', '', $data['callbackQuery']),
            ]);
        }


        if (str_starts_with($data['callbackQuery'], TelegramCallbackAction::ToConsultation->value)) {
            return new self(TelegramCallbackAction::ToConsultation->value, $data['clientBotId'], [
                TelegramCallbackAction::ToConsultation->value => str_replace(TelegramCallbackAction::ToConsultation->value, '', $data['callbackQuery']),
            ]);
        }
        if ($data['callbackQuery'] === TelegramCallbackAction::ToMain->value) {
            return new self(TelegramCallbackAction::ToMain->value, $data['clientBotId']);
        }
        if ($data['callbackQuery'] === TelegramCallbackAction::Cancel->value) {
            return new self(TelegramCallbackAction::Cancel->value, $data['clientBotId']);
        }

        if ($data['callbackQuery'] === TelegramCallbackAction::SelectCountryBack->value) {
            return new self(TelegramCallbackAction::SelectCountryBack->value, $data['clientBotId']);
        }

        if ($data['callbackQuery'] === TelegramCallbackAction::SelectBankBack->value) {
            return new self(TelegramCallbackAction::SelectBankBack->value, $data['clientBotId']);
        }

        if ($data['callbackQuery'] === TelegramCallbackAction::SelectAmountBack->value) {
            return new self(TelegramCallbackAction::SelectAmountBack->value, $data['clientBotId']);
        }

        if ($data['callbackQuery'] === TelegramCallbackAction::SelectCurrencyBack->value) {
            return new self(TelegramCallbackAction::SelectCurrencyBack->value, $data['clientBotId']);
        }


        return new self('unknown');
    }

}
