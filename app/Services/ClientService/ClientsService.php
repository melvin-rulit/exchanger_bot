<?php

namespace App\Services\ClientService;

use App\Models\Client;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class ClientsService implements ClientServiceInterface
{
    public function getClient($clientId)
    {
        return Client::where('bot_id', $clientId)->first();
    }
    public function getClientLanguage($clientId)
    {
        $client = Client::where('bot_id', $clientId)->first();
        $setting = $client->settings->where('key', 'language')->first();
        return $setting['value'];
    }

    public function setStatus($clientId, $status): void
    {
        $client = Client::where('bot_id', $clientId)->first();

        if ($client) {
            $client->status = $status;
            $client->save();
        }
    }

    public function setClientChangeLanguageInput($clientId, $language, $setStatus)
    {
        $client = Client::where('bot_id', $clientId)->first();

        if ($client) {
                $client->status = 'language_input';
                $client->save();

            if (!$setStatus) {
                $setting_language = Setting::where('value', $language)->first();
                $client_language_setting = $client->settings->where('key', 'language')->first();

                if ($setting_language && $client_language_setting) {
                    $client_language_setting->pivot->setting_id = $setting_language->id;
                    $client_language_setting->pivot->save();
                    return true;
                } else {
//                    $client->settings()->attach($setting_language->id, ['value' => $language]);
                }
            }
        } else {
            Log::error("Client with bot_id {$clientId} not found.");
        }

        return false;
    }

    public function isClientChangeLanguageInput($clientId)
    {
        $client = Client::where('bot_id', $clientId)->first();
    }
    public function checkIfClientExit($data): void
    {
        $from = $data['message']['from'];
        $clientId = $data['message']['from']['id'];

        if (!$from['is_bot']) {

            $client = Client::where('bot_id', $clientId)->first();


            if (!$client) {
                $client = new Client();
                $client->bot_id = $clientId;
                $client->first_name = $from['first_name'];
                $client->bot_name = $from['username'];
                $client->status = 'main_menu';
                $client->save();

                //Устанавливаем дефолтные настройки русского языка
                $client->settings()->attach(1);
            }
        }
    }
    public function setClientMainInput($clientId, $status): void
    {
        $this->setStatus($clientId, $status);
    }
    public function setClientAmountInput($clientId): void
    {
        $this->setStatus($clientId, 'amount_input');
    }
    public function setClientAmountSuccessInput($clientId): void
    {
        $this->setStatus($clientId, 'amount_input_success');
    }
    public function setClientCurrencyInput($clientId): void
    {
        $this->setStatus($clientId, 'currency_input');
    }

    public function isUserInAmountInput($clientId): bool
    {
        $isUserInAmountInput = Client::where('bot_id', $clientId)->first();

        if ($isUserInAmountInput) {
            if ($isUserInAmountInput->status == 'amount_input') {
                return true;
            }
        }
        return false;
    }
    public function setUserCountryInput($clientId): void
    {
        $this->setStatus($clientId, 'country_input');
    }

    public function isUserInACountryInput($clientId): bool
    {
        $isUserInACountryInput = Client::where('bot_id', $clientId)->first();

        if ($isUserInACountryInput) {
            if ($isUserInACountryInput->status == 'country_input') {
                return true;
            }
        }
        return false;
    }

    public function setClientBankInput($clientId): void
    {
        $this->setStatus($clientId, 'bank_input');
    }

    public function setClientConsultationInput($clientId): void
    {
        $this->setStatus($clientId, 'consultant_input');
    }
    public function isClientConsultationInput($clientId): bool
    {
        $isClientConsultationInput = Client::where('bot_id', $clientId)->first();

        if ($isClientConsultationInput) {
            if ($isClientConsultationInput->status == 'consultant_input') {
                return true;
            }
        }
        return false;
    }

    public function setClientSendScreenshot($clientId): void
    {
        $this->setStatus($clientId, 'send_screenshot');
    }
    public function isClientSendScreenshot($clientId): bool
    {
        $isClientConsultationInput = Client::where('bot_id', $clientId)->first();

        if ($isClientConsultationInput) {
            if ($isClientConsultationInput->status == 'send_screenshot') {
                return true;
            }
        }
        return false;
    }
}
