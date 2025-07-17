<?php

namespace App\Actions;

use App\Models\Country;
use App\DTO\CountrySelectionData;
use App\Enums\Country\CountryField;
use App\Enums\TelegramCallbackAction;
use App\Exceptions\TelegramApiException;
use App\Services\RedisService\RedisMessageService;
use App\Telegram\Keyboard\KeyboardFactory;
use App\Services\ClientService\ClientsService;
use App\Services\RedisService\RedisSessionService;
use App\Exceptions\Country\CountryNotFoundException;
use App\Services\TelegramBotService\TelegramMessageService;

class StartCountryAction
{
    public function __construct(protected ClientsService $clientsService, protected TelegramMessageService $telegramMessageService, protected RedisSessionService $redis, protected RedisMessageService $redisMessageService) {}

    /**
     * @throws TelegramApiException
     * @throws CountryNotFoundException
     */
    public function execute(CountrySelectionData $data): void
    {
        $this->redis->forgetCountryConsultant($data->chatId);
        $language = $this->clientsService->getClientLanguage($data->clientBotId);
        $this->clientsService->setUserCountryInput($data->clientBotId);

        $countries = Country::where('is_used', true)->get();

        if ($countries->isEmpty()) {
            throw new CountryNotFoundException("Ни одной страны is_used = true не найдено", 404, null, ['file', 'StartCountryAction:30'], 'database');
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
        $this->telegramMessageService->deleteMessages($data->chatId);

        $stepOneMessage = $this->telegramMessageService->sendDeleteReplay($data->chatId, 'Шаг 1');
        $getCountry = $this->telegramMessageService->sendMessageWithButtons($data->chatId, __('messages.get_country'), $keyboard, $data->messageId);

        $this->redisMessageService->setDeletedMessageForChat($data->chatId, $stepOneMessage);
        $this->redisMessageService->setDeletedMessageForChat($data->chatId, $getCountry);
    }
}
