<?php

namespace App\Actions;

use App\Models\Country;
use App\DTO\RequisiteSelectionData;
use App\Enums\TelegramCallbackAction;
use App\Telegram\Keyboard\KeyboardFactory;
use App\Services\ClientService\ClientsService;
use App\Services\TelegramBotService\TelegramMessageService;

class StartRequisiteAction
{
    public function __construct(protected ClientsService $clientsService, protected TelegramMessageService $telegramMessageService) {}

    public function execute(RequisiteSelectionData $data): void
    {

        $language = $this->clientsService->getClientLanguage($data->clientBotId);
        $this->clientsService->setUserCountryInput($data->clientBotId);

        $countries = Country::where('is_used', true)->get();

        $keyboard = [
            'inline_keyboard' => [],
        ];

        $countryNameField = ($language === 'ru') ? 'name_ru' : 'name_en';
        $row = [];

        foreach ($countries as $index => $country) {
            $row[] = [
                'text' => ($country->flag ?? '') . ' ' . $country->$countryNameField,
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

        $keyboard['inline_keyboard'][] = KeyboardFactory::toBack(TelegramCallbackAction::SelectCountryBack);

        $this->telegramMessageService->sendMessageWithButtons($data->chatId, __('messages.get_country'), $keyboard, $data->messageId);
        $this->telegramMessageService->deleteMessage($data->chatId, $data->messageId );
        $this->telegramMessageService->deleteMessage($data->chatId, $data->messageId -1);
        $this->telegramMessageService->deleteMessage($data->chatId, $data->messageId -2);
    }

}
