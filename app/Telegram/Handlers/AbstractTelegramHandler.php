<?php

namespace App\Telegram\Handlers;

use App\Handlers\ConsultationMessageHandler;
use App\Handlers\LanguageMessageHandler;
use App\Services\ActionService\CountryActionService;
use App\Services\ChatService\ChatService;
use App\Services\ClientService\ClientsService;
use App\Services\CredentialService\CredentialService;
use App\Services\OrderService\OrderService;
use App\Services\RedisSessionService;
use App\Services\TelegramBotService\TelegramMessageService;
use App\Telegram\Route\CallbackMenuRouter;
use App\Telegram\Route\CallbackRouter;
use App\Telegram\Traits\HandlesImageDownload;
use App\Telegram\Traits\SavePhoto;

abstract class AbstractTelegramHandler
{
    use SavePhoto, HandlesImageDownload;

    protected string $url;

    public function __construct(
        protected TelegramMessageService $telegramMessageService,
        protected ChatService $chatService,
        protected ClientsService $clientsService,
        protected OrderService $orderService,
        protected CredentialService $credentialService,
        protected CountryActionService $countryActionService,
        protected RedisSessionService $redis,
        protected ConsultationMessageHandler $consultationMessageHandler,
        protected LanguageMessageHandler $languageMessageHandler,
        protected CallbackRouter $callbackRouter,
        protected CallbackMenuRouter $callbackMenuRouter
    ) {
        $this->url = ensure_string(config('telegram.telegram_bot.api_url')) . ensure_string(config('telegram.telegram_bot.token'));
    }
}
