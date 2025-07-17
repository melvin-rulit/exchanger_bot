<?php

namespace App\Actions;

use App\Models\Bank;
use App\Models\Country;
use App\DTO\BankSelectionData;
use App\Enums\Bank\BankField;
use App\Enums\TelegramCallbackAction;
use App\Exceptions\TelegramApiException;
use App\Telegram\Keyboard\KeyboardFactory;
use App\Services\ClientService\ClientsService;
use App\Services\RedisService\RedisMessageService;
use App\Services\RedisService\RedisSessionService;
use App\Exceptions\Country\CountryNotFoundException;
use App\Exceptions\Country\CountryBankNotFoundException;
use App\Services\TelegramBotService\TelegramMessageService;

class StartBankAction
{
    public function __construct(protected ClientsService $clientsService, protected TelegramMessageService $telegramMessageService, protected RedisSessionService $redis, protected RedisMessageService $redisMessageService) {}

    /**
     * @throws TelegramApiException
     * @throws CountryNotFoundException
     * @throws CountryBankNotFoundException
     */
    public function execute(BankSelectionData $data): void
    {
        $this->redis->forgetBankConsultant($data->chatId);

        $country = Country::with('banks')->where('code', $data->countryCode)->first();

        if (Country::count() === 0) {
            throw new CountryNotFoundException("Страна с кодом {$data->countryCode} не найдена", 404, null, [], 'database');
        }

        if ($country->banks->isEmpty()) {
            throw new CountryBankNotFoundException('У выбранной страны нет доступных банков.', 404, null, [], 'database');
        }

        $this->redis->setSelectedCountry($data->chatId, $country->id);
        $this->redis->setCountryCode($data->chatId, $data->countryCode);

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

        $this->clientsService->setClientBankInput($data->clientBotId);
        $this->telegramMessageService->deleteMessages($data->chatId);

        $stepTwoMessage = $this->telegramMessageService->sendDeleteReplay($data->chatId, 'Шаг 2');
        $getBank = $this->telegramMessageService->sendMessageWithButtons($data->chatId, __('messages.get_banks'), $keyboard, $data->messageId);

        $this->redisMessageService->setDeletedMessageForChat($data->chatId, $stepTwoMessage);
        $this->redisMessageService->setDeletedMessageForChat($data->chatId, $getBank);
    }
}
