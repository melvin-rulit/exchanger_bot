<?php

namespace App\Actions;

use App\Models\Country;
use App\DTO\CountrySelectionData;
use App\Services\RedisSessionService;
use App\Telegram\Keyboard\KeyboardFactory;
use App\Services\ClientService\ClientsService;
use App\Services\TelegramBotService\TelegramMessageService;

class StartCountryAction
{
    public function __construct(protected ClientsService $clientsService, protected TelegramMessageService $telegramMessageService, protected RedisSessionService $redis) {}
    public function execute(CountrySelectionData $data): void
    {
        $country = Country::with('banks')->where('code', $data->countryCode)->first();

        $this->redis->setSelectedCountry($data->chatId, $country->id);
        $this->redis->setCountryCode($data->chatId, $data->countryCode);

        $keyboard = [
            'inline_keyboard' => []
        ];

        $row = [];

        foreach ($country->banks as $index => $bank) {
            $row[] = [
                'text' => $bank->name,
                'callback_data' => 'bank_' . $bank->id
            ];
            if (count($row) == 2) {
                $keyboard['inline_keyboard'][] = $row;
                $row = [];
            }
        }
        if (count($row) > 0) {
            $keyboard['inline_keyboard'][] = $row;
        }
        $keyboard['inline_keyboard'][] = KeyboardFactory::toBack('select_bank_back');

        $this->clientsService->setClientBankInput($data->clientBotId);

        $this->telegramMessageService->deleteMessage($data->chatId, $data->messageId);
        $this->telegramMessageService->sendMessageWithButtons($data->chatId, __('messages.get_banks'), $keyboard, $data->messageId);
    }
}
