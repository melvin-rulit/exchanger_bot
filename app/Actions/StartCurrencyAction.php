<?php

namespace App\Actions;

use App\DTO\CurrencySelectionData;
use App\Services\ClientService\ClientsService;
use App\Services\RedisSessionService;
use App\Services\TelegramBotService\TelegramMessageService;

use Illuminate\Support\Facades\Redis;

class StartCurrencyAction
{
    public function __construct(protected ClientsService $clientsService, protected TelegramMessageService $telegramMessageService, protected RedisSessionService $redis) {}
    public function execute(CurrencySelectionData $data): void
    {
        Redis::set('select_currency_for_' . $data->clientBotId, $data->currencyId);

        //        $keyboard = [
//            'keyboard' => [
//                [
//                    ['text' => __('buttons.to_main')]
//                ],
//                [
//                    ['text' => __('buttons.consultation')]
//                ],
//            ],
//            'resize_keyboard' => true,
//            'one_time_keyboard' => true,
//        ];

        $this->clientsService->setClientAmountInput($data->clientBotId);

        $this->telegramMessageService->deleteMessage($data->chatId, $data->messageId);
        $this->telegramMessageService->sendMessage($data->chatId, __('messages.enter_the_amount_only_numbers'), $data->messageId);

    }
}
