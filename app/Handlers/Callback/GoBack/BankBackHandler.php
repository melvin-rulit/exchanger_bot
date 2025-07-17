<?php

namespace App\Handlers\Callback\GoBack;

use App\Models\Country;
use App\Enums\Country\CountryField;
use App\Enums\TelegramCallbackAction;
use App\Exceptions\TelegramApiException;
use App\Services\RedisService\RedisMessageService;
use App\Telegram\Keyboard\KeyboardFactory;
use App\Services\ClientService\ClientsService;
use App\Exceptions\Country\CountryNotFoundException;
use App\Services\TelegramBotService\TelegramMessageService;

class BankBackHandler
{
    public function __construct(protected TelegramMessageService $telegramMessageService, protected  ClientsService $clientsService, protected RedisMessageService $redisMessageService) {}

    /**
     * @throws TelegramApiException
     * @throws CountryNotFoundException
     */
    public function handle(int $clientBotId, int|string $chatId, int $messageId): void
    {
        $language = $this->clientsService->getClientLanguage($clientBotId);
        $this->clientsService->setUserCountryInput($clientBotId);

        $countries = Country::where('is_used', true)->get();

        if ($countries->isEmpty()) {
            throw new CountryNotFoundException("Ни одной страны is_used = true не найдено", 404, null, ['file', 'BankBackHandler:26'], 'database');
        }

        $keyboard = [
            'inline_keyboard' => [],
        ];

        $row = [];

        /** @var Country[] $countries */
        foreach ($countries as $country) {
            $row[] = [
                'text' => ($country->flag ?? '') . ' ' . $country->getLocalizedName($language),
                'callback_data' => 'country_' . $country->code
            ];

            if (count($row) == 2) {
                $keyboard['inline_keyboard'][] = $row;
                $row = [];
            }
        }

        if (count($row) > 0) {
            $keyboard['inline_keyboard'][] = $row;
        }

        $keyboard['inline_keyboard'][] = KeyboardFactory::toConsultation(TelegramCallbackAction::ToConsultation->value. CountryField::COUNTRY->value);
        $keyboard['inline_keyboard'][] = KeyboardFactory::toBack(TelegramCallbackAction::SelectCountryBack);

        $this->telegramMessageService->deleteMessages($chatId);
        $stepOneMessage = $this->telegramMessageService->sendDeleteReplay($chatId, 'Шаг 1');
        $getCountry = $this->telegramMessageService->sendMessageWithButtons($chatId, __('messages.get_country'), $keyboard, $messageId);

        $this->redisMessageService->setDeletedMessageForChat($chatId, $stepOneMessage);
        $this->redisMessageService->setDeletedMessageForChat($chatId, $getCountry);
    }
}
