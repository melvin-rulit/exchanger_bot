<?php

namespace App\Telegram\Handlers;

use App\Exceptions\TelegramApiException;
use App\Services\ChatService\ChatService;
use App\Services\RedisService\RedisMessageService;
use App\Telegram\Keyboard\KeyboardFactory;
use App\Services\ClientService\ClientsService;
use App\Services\RedisService\RedisSessionService;
use App\Exceptions\Services\MessageNotFoundException;
use App\Services\TelegramBotService\TelegramMessageService;

class StartHandles
{
    public function __construct(protected ClientsService $clientsService, protected TelegramMessageService $telegramMessageService,  protected ChatService $chatService, protected RedisSessionService $redis, protected RedisMessageService $redisMessageService){}

    /**
     * @throws TelegramApiException
     */
    public function sendStartMessageWithButtons($chatId, $clientId, $messageId): void
    {
        $keyboard = KeyboardFactory::startKeyboard();

        $this->clientsService->setClientMainInput($clientId, __('buttons.to_main'));

        $greetingMessage = $this->telegramMessageService->sendMessageWithButtons($chatId, __('messages.greeting'), $keyboard, $messageId);

        if ($greetingMessage){
            $this->redisMessageService->setDeletedMessageForChat($chatId, $greetingMessage);
        }
    }

    /**
     * @throws MessageNotFoundException
     */
    public function checkStartMessage($text, $callback): string
    {
       // \Log::info(print_r($callback, true));
        $this->redisMessageService->setDeletedMessageForChat($callback->chatId, $callback->messageId);

        if ($this->clientsService->isClientConsultationInput($callback->clientBotId)) {
            $this->clientsService->setCloseConsultation($this->redis->getLastMessageIdFromClient($callback->chatId));
        }

        $this->clientsService->setClientMainInput($callback->clientBotId, __('buttons.to_main'));
        return $text === '/start';
    }

    public function checkMainMenu($text): bool
    {
        if ($text === __('buttons.to_main') || $text === __('buttons.cancel')) {
//            $this->clientsService->setClientMainInput($clientId, __('buttons.to_main'));
//            $this->sendStartMessageWithButtons($chatId, $messageId, $clientId);
            return true;
        }
        return false;
    }
}
