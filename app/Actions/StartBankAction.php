<?php

namespace App\Actions;

use App\Models\Currency;
use App\DTO\BankSelectionData;
use App\Services\RedisSessionService;
use App\Telegram\Keyboard\KeyboardFactory;
use App\Services\ClientService\ClientsService;
use App\Services\TelegramBotService\TelegramMessageService;
use Illuminate\Support\Facades\Log;

class StartBankAction
{
    public function __construct(protected ClientsService $clientsService, protected TelegramMessageService $telegramMessageService, protected RedisSessionService $redis) {}
    public function execute(BankSelectionData $data): void
    {
        $country_currency = Currency::where('country_id', $this->redis->getSelectedCountry($data->chatId))->get();

        $keyboard = [
            'inline_keyboard' => [],
        ];

        if ($country_currency) {
            $row = [];

            foreach ($country_currency as $index => $currency) {
                $row[] = [
                    'text' => $currency->name,
                    'callback_data' => 'currency_' . $currency->id
                ];
                if (count($row) == 3) {
                    $keyboard['inline_keyboard'][] = $row;
                    $row = [];
                }
            }
            if (count($row) > 0) {
                $keyboard['inline_keyboard'][] = $row;
            }

            $keyboard['inline_keyboard'][] = KeyboardFactory::toBack('select_currency_back');
        }

        $this->clientsService->setClientCurrencyInput($data->clientBotId);

        $this->telegramMessageService->deleteMessage($data->chatId, $data->messageId);
        $this->telegramMessageService->sendMessageWithButtons($data->chatId, __('messages.get_currency'), $keyboard, $data->messageId);
    }
}
