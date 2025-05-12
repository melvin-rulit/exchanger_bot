<?php

namespace App\Handlers\Callback\GoBack;

use App\Models\Bank;
use App\Models\Country;
use App\Exceptions\TelegramApiException;
use App\Telegram\Keyboard\KeyboardFactory;
use App\Services\ClientService\ClientsService;
use App\Services\RedisService\RedisSessionService;
use App\Services\TelegramBotService\TelegramMessageService;

class CurrencyBackHandler
{
    public function __construct(protected TelegramMessageService $telegramMessageService, protected  ClientsService $clientsService, protected RedisSessionService $redis) {}

    /**
     * @throws TelegramApiException
     */
    public function handle(int $clientBotId, int $chatId, int $messageId): void
    {
        $country = Country::with('banks')->where('code', $this->redis->getCountryCode($chatId))->first();

        $this->redis->setSelectedCountry($chatId, $country->id);
        $this->redis->setCountryCode($chatId, $this->redis->getCountryCode($chatId));

        $keyboard = [
            'inline_keyboard' => [],
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

//        $keyboard['inline_keyboard'][] = KeyboardFactory::toConsultation(TelegramCallbackAction::ToConsultation);
        $keyboard['inline_keyboard'][] = KeyboardFactory::toBack('select_bank_back');

        $this->clientsService->setClientBankInput($clientBotId);

        $this->telegramMessageService->deleteMessage($chatId, $messageId);
        $this->telegramMessageService->sendMessageWithButtons($chatId, __('messages.get_banks'), $keyboard, $messageId);
    }
}
