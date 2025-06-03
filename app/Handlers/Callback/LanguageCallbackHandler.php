<?php

namespace App\Handlers\Callback;

use Illuminate\Support\Facades\App;
use App\Exceptions\TelegramApiException;
use App\Telegram\Keyboard\KeyboardFactory;
use App\Services\ClientService\ClientsService;
use App\Services\RedisService\RedisSessionService;
use App\Services\TelegramBotService\TelegramMessageService;

class LanguageCallbackHandler
{
    public function __construct(protected ClientsService $clientsService,protected TelegramMessageService $telegramMessageService, protected RedisSessionService $redisSessionService) {}

    /**
     * @throws TelegramApiException
     */
    public function handle(string $languageCode, int $clientBotId, int|string $chatId, int $messageId): void {

        $isSetLanguage = $this->clientsService->setClientChangeLanguageInput($clientBotId, $languageCode, false);

        if ($isSetLanguage) {
            $this->clientsService->setClientMainInput($clientBotId, __('buttons.to_main'));

            $language = $this->clientsService->getClientLanguage($clientBotId);

            App::setLocale($language);

            $keyboard = KeyboardFactory::startKeyboard();

            $this->telegramMessageService->deleteMessage($chatId, $messageId);
            $this->telegramMessageService->sendMessageWithButtons($chatId, __('messages.greeting'), $keyboard, $messageId);
        }
    }
}
