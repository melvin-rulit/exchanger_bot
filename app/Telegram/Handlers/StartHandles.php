<?php

namespace App\Telegram\Handlers;

use App\Exceptions\TelegramApiException;
use App\Services\ChatService\ChatService;
use App\Telegram\Keyboard\KeyboardFactory;
use App\Services\ClientService\ClientsService;
use App\Services\RedisService\RedisSessionService;
use App\Exceptions\Services\MessageNotFoundException;
use App\Services\TelegramBotService\TelegramMessageService;
use Illuminate\Support\Facades\Log;

class StartHandles
{
    public function __construct(protected ClientsService $clientsService, protected TelegramMessageService $telegramMessageService,  protected ChatService $chatService, protected RedisSessionService $redis){}

    /**
     * @throws TelegramApiException
     */
    public function sendStartMessageWithButtons($chatId, $clientId, $messageId): void
    {
        $keyboard = KeyboardFactory::startKeyboard();

        $this->clientsService->setClientMainInput($clientId, __('buttons.to_main'));
//TODO определить нужно ли тут условие
//        $this->sendMessageWithButton($chatId, $keyboard, __('messages.greeting'), $messageId);
        $this->telegramMessageService->sendMessageWithButtons($chatId, __('messages.greeting'), $keyboard, $messageId);


//        if(!$this->isInMainMenu){
//
//            $this->sendMessageWithButton($chatId, $keyboard, __('messages.greeting'), $messageId);
//        }
    }

    /**
     * @throws MessageNotFoundException
     */
    public function checkStartMessage($text, $callback): string
    {
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
