<?php

namespace App\Actions;

use App\DTO\LanguageSelectionData;
use App\Telegram\Keyboard\KeyboardFactory;
use App\Services\ClientService\ClientsService;
use App\Services\TelegramBotService\TelegramMessageService;

class StartLanguageAction
{

    public function __construct(protected ClientsService $clientsService, protected TelegramMessageService $telegramMessageService) {}
    public function execute(LanguageSelectionData $data): void
    {
        $this->clientsService->setClientChangeLanguageInput($data->clientId, null, true);

        $keyboard = KeyboardFactory::languageKeyboard();

        $this->telegramMessageService->sendMessageWithButtons($data->chatId, __('messages.get_language'), $keyboard, null);
        $this->telegramMessageService->deleteMessage($data->chatId, $data->messageId );
        $this->telegramMessageService->deleteMessage($data->chatId, $data->messageId -1);
        $this->telegramMessageService->deleteMessage($data->chatId, $data->messageId -2);


//        $this->telegramMessageService->sendMessage($chatId, <<<HTML
//<b>Добро пожаловать!</b>
//
//Выберите <i>язык</i> интерфейса или <a href="https://example.com">узнайте больше</a>.
//
//<code>Это монокод</code>
//
//<pre>
//А это блок кода
//    с отступами
//</pre>
//HTML
//        );

    }
}
