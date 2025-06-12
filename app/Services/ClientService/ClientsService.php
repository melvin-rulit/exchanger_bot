<?php

namespace App\Services\ClientService;

use App\Models\Client;
use App\Models\Message;
use App\Models\Setting;
use App\Services\BaseService;
use App\Enums\MenuLevelStatus;
use App\DTO\CallbackTelegramData;
use App\Telegram\Traits\HandlesFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Exceptions\TelegramApiException;
use App\Events\Consultation\ConsultationClosed;
use App\Exceptions\Images\MediaLibraryException;
use App\Services\RedisService\RedisSessionService;
use App\Exceptions\Services\MessageNotFoundException;
use App\Exceptions\Helpers\InvalidStringValueException;

class ClientsService extends BaseService implements ClientServiceInterface
{
    use HandlesFile;

    protected string $url;
    protected string $download_url;
    public function __construct(RedisSessionService $redis)
    {
        parent::__construct($redis);
        $this->url = ensure_string(config('telegram.telegram_bot.api_url'), 'telegram.telegram_bot.api_url') . ensure_string(config('telegram.telegram_bot.token'), 'telegram.telegram_bot.token');
        $this->download_url = ensure_string(config('telegram.telegram_bot.api_file_url')) . config('telegram.telegram_bot.token');
    }

    public function getClient($clientBotId, $chatId): ?int
    {
        if (!$this->redis->has('client_id', $chatId)){

            $client = $this->getClientByBotId($clientBotId);
            $this->redis->setClientIdForChat($chatId, $client->id, 0);
        }

        return $this->redis->getClientIdForChat($chatId);
    }

    public function getClientLanguage($clientBotId): string
    {
        $client = $this->getClientByBotId($clientBotId);
        $setting = $client->settings->where('key', 'language')->first();
        return $setting['value'];
    }

    public function setClientChangeLanguageInput($clientBotId, $language, $setStatus): true
    {
        if ($client = $this->getClientByBotId($clientBotId)) {
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
            Log::error("Client with bot_id {$clientBotId} not found.");
        }

        return true;
    }

    public function isClientChangeLanguageInput($clientBotId): void
    {
        $client = $this->getClientByBotId($clientBotId);
    }

    /**
     * @throws InvalidStringValueException
     * @throws TelegramApiException
     * @throws MediaLibraryException
     */
    public function checkIfClientExit($data): void
    {
        $callback = CallbackTelegramData::fromWebhook($data);
        $botToken = ensure_string(config('telegram.telegram_bot.token'));
        if (!$callback->fromBot) {

            $client = Client::where('bot_id', $callback->clientBotId)->first();

            if (!$client) {
                $client = new Client();
                $client->bot_id = $callback->clientBotId;
                $client->first_name = $callback->firsName;
                $client->bot_name = $callback->userName;
                $client->status = 'main_menu';
                $client->save();

                $getPhotosUrl = "https://api.telegram.org/bot{$botToken}/getUserProfilePhotos?user_id={$client->bot_id}&limit=1";
                $response = file_get_contents($getPhotosUrl);
                $data = json_decode($response, true);

                if ($data['ok'] && $data['result']['total_count'] > 0) {
                    $fileId = $data['result']['photos'][0][0]['file_id'];

                    $imageContent = $this->getTelegramFileContent($fileId);

                    $this->saveImageToModelFromResponse($imageContent, 'client_avatar.jpg', $client, 'client_avatar', false);

                } else {
                    echo "Фото профиля отсутствует.";
                }
//                $this->saveImageToModelFromResponse($imageContent, 'screenshot.jpg', $created_message, 'chat_screenshot');

                //Устанавливаем дефолтные настройки русского языка
                $client->settings()->attach(1);
                $this->redis->setClientIdForChat($callback->chatId, $client->id, 0);
            }
        }
    }
    public function setClientMainInput($clientId, $status): true
    {
        return $this->setStatus($clientId, $status);
    }
    public function setUserCountryInput($clientId): void
    {
        $this->setStatus($clientId, MenuLevelStatus::Country->value);
    }
    public function setClientBankInput($clientId): void
    {
        $this->setStatus($clientId, MenuLevelStatus::Bank->value);
    }
    public function setClientCurrencyInput($clientId): void
    {
        $this->setStatus($clientId, MenuLevelStatus::Currency->value);
    }

    public function setClientAmountInput($clientId): void
    {
        $this->setStatus($clientId, MenuLevelStatus::Amount->value);
    }

    public function setRequisiteInput($clientId): void
    {
        $this->setStatus($clientId, MenuLevelStatus::Requisite->value);
    }
    public function setClientAmountSuccessInput($clientId): void
    {
        $this->setStatus($clientId, 'amount_input_success');
    }
    public function setClientSendScreenshot($clientId): void
    {
        $this->setStatus($clientId, MenuLevelStatus::Screenshot->value);
    }
    public function setClientWalletInput($clientId): void
    {
        $this->setStatus($clientId, MenuLevelStatus::Wallet->value);
    }
    public function setClientConsultationInput($chatId, $clientId): void
    {
        $this->setStatus($clientId, MenuLevelStatus::Consultant->value);
        $this->redis->setClientInConsultation($chatId);

        if ($this->redis->getClientInConsultation($chatId)){
            if (!$this->redis->has('message_group_in_consultation', $chatId)) {
                $randomCharacter = generateRandomDigits();
                $this->redis->setMessageGroupForConsultation($chatId, $randomCharacter, 0);
            }
        }
    }

    /**
     * @throws MessageNotFoundException
     */
    public function setCloseConsultation($messageId): void
    {
        if (!$message = Message::find($messageId)) {
            throw new MessageNotFoundException("Заказ с ID {$messageId} не найден.");
        }

        $message->is_close = true;
        $message->save();
        //$this->clearConsultationSession($clientId);

        broadcast( new ConsultationClosed());
    }
    public function isClientInACountryInput($clientBotId): bool
    {
        return $this->getClientStatus($clientBotId) === MenuLevelStatus::Country->value;
    }

    public function isClientInBankInput($clientBotId): bool
    {
        return $this->getClientStatus($clientBotId) === MenuLevelStatus::Bank->value;
    }
    public function isUserInAmountInput($clientBotId): bool
    {
        return $this->getClientStatus($clientBotId) === MenuLevelStatus::Amount->value;
    }
    public function isUserInRequisiteInput($clientBotId): bool
    {
        return $this->getClientStatus($clientBotId) === MenuLevelStatus::Requisite->value;
    }

    public function isClientConsultationInput($clientBotId): bool
    {
        return $this->getClientStatus($clientBotId) === MenuLevelStatus::Consultant->value;
    }

    public function isClientSendScreenshot($clientBotId): bool
    {
        return $this->getClientStatus($clientBotId) === MenuLevelStatus::Screenshot->value;
    }
    public function isClientWalletInput($clientBotId): bool
    {
        return $this->getClientStatus($clientBotId) === MenuLevelStatus::Wallet->value;
    }
    public function clearConsultationSession($clientId): void
    {
        Redis::set('client_in_consultation' . $clientId, false);
        Redis::del(['message_group_in_consultation' . $clientId]);
    }
}
