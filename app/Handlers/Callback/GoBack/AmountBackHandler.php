<?php

namespace App\Handlers\Callback\GoBack;

use App\Models\Bank;
use App\Models\Country;
use App\Enums\Bank\BankField;
use App\Enums\TelegramCallbackAction;
use App\Exceptions\TelegramApiException;
use App\Telegram\Keyboard\KeyboardFactory;
use App\Services\ClientService\ClientsService;
use App\Services\RedisService\RedisSessionService;
use App\Exceptions\Country\CountryNotFoundException;
use App\Exceptions\Country\CountryBankNotFoundException;
use App\Services\TelegramBotService\TelegramMessageService;

class AmountBackHandler
{
    public function __construct(protected TelegramMessageService $telegramMessageService, protected  ClientsService $clientsService, protected RedisSessionService $redis) {}

    /**
     * @throws TelegramApiException
     * @throws CountryNotFoundException
     * @throws CountryBankNotFoundException
     */
    public function handle(int $clientBotId, int $chatId, int $messageId): void
    {
        $country = Country::with('banks')->where('code',  $this->redis->getCountryCode($chatId))->first();

        if (Country::count() === 0) {
            throw new CountryNotFoundException("Страна с кодом {$this->redis->getCountryCode($chatId)} не найдена", 404, null, [], 'database');
        }

        if ($country->banks->isEmpty()) {
            throw new CountryBankNotFoundException('У выбранной страны нет доступных банков.', 404, null, [], 'database');
        }

        $keyboard = [
            'inline_keyboard' => []
        ];

        $row = [];

        /** @var Bank $bank */
        foreach ($country->banks as $bank) {
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
        $keyboard['inline_keyboard'][] = KeyboardFactory::toConsultation(TelegramCallbackAction::ToConsultation->value. BankField::BANK->value);
        $keyboard['inline_keyboard'][] = KeyboardFactory::toBack(TelegramCallbackAction::SelectBankBack->value);

        $this->clientsService->setClientBankInput($clientBotId);

        $this->telegramMessageService->deleteMessage($chatId, $messageId);
        $this->telegramMessageService->sendDeleteReplay($chatId);
        $this->telegramMessageService->sendMessageWithButtons($chatId, __('messages.get_banks'), $keyboard, $messageId);
    }
}
