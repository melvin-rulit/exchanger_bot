<?php

namespace App\Telegram\Handlers;

use App\Telegram\Traits\SavePhoto;
use App\Telegram\Traits\HandlesFile;
use App\Telegram\Route\CallbackRouter;
use App\Handlers\LanguageMessageHandler;
use App\Services\ChatService\ChatService;
use App\Telegram\Route\CallbackMenuRouter;
use App\Handlers\RequisiteCallbackHandler;
use App\Services\OrderService\OrderService;
use App\Handlers\ConsultationMessageHandler;
use App\Services\ClientService\ClientsService;
use App\Telegram\Traits\LoadTelegramBotService;
use App\Telegram\Route\CallbackMenuReturnRouter;
use App\Services\RedisService\RedisFieldService;
use App\Services\RedisService\RedisSessionService;
use App\Services\ActionService\CountryActionService;
use App\Services\CredentialService\CredentialService;
use App\Exceptions\Helpers\InvalidStringValueException;
use App\Services\TelegramBotService\TelegramMessageService;

abstract class AbstractTelegramHandler
{
    use SavePhoto, HandlesFile, LoadTelegramBotService;

    protected string $url;
    protected string $download_url;

    /**
     * @throws InvalidStringValueException
     */
    public function __construct(
        protected StartHandles $startHandles,
        protected TelegramMessageService $telegramMessageService,
        protected ChatService $chatService,
        protected ClientsService $clientsService,
        protected OrderService $orderService,
        protected CredentialService $credentialService,
        protected CountryActionService $countryActionService,
        protected RedisSessionService $redis,
        protected RedisFieldService $redisField,
        protected ConsultationMessageHandler $consultationMessageHandler,
        protected LanguageMessageHandler $languageMessageHandler,
        protected CallbackRouter $callbackRouter,
        protected CallbackMenuRouter $callbackMenuRouter,
        protected CallbackMenuReturnRouter $callbackMenuReturnRouter,
        protected RequisiteCallbackHandler $requisiteCallbackHandler,
        protected ConsultationHandles $consultationHandles,
        protected ReceiptHandler $receiptHandler
    ) {
        $this->url = ensure_string(config('telegram.telegram_bot.api_url'), 'telegram.telegram_bot.api_url') . ensure_string(config('telegram.telegram_bot.token'), 'telegram.telegram_bot.token');
        $this->download_url = ensure_string(config('telegram.telegram_bot.api_file_url')) . config('telegram.telegram_bot.token');
    }
}
