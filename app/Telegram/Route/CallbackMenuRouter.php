<?php

namespace App\Telegram\Route;

use App\DTO\CallbackMenu;
use Illuminate\Support\Facades\Log;
use App\Handlers\Callback\MenuHandlers\GetLanguageCallbackHandler;
use App\Handlers\Callback\MenuHandlers\GetRequisiteCallbackHandler;
use App\Handlers\Callback\MenuHandlers\GetConsultationCallbackHandler;

class CallbackMenuRouter
{
    public function __construct(protected GetRequisiteCallbackHandler $getRequisiteCallbackHandler, protected GetConsultationCallbackHandler $getConsultationCallbackHandler, protected GetLanguageCallbackHandler $getLanguageCallbackHandler){}

    public function route(CallbackMenu $callbackData, int $clientBotId, int $chatId, int $messageId): void
    {
        match ($callbackData->action) {

            __('buttons.get_requisite') => $this->getRequisiteCallbackHandler->handle($clientBotId, $chatId, $messageId),
            '👩‍💻 ' . __('buttons.consultation') => $this->getConsultationCallbackHandler->handle($clientBotId, $chatId, $messageId),
            __('buttons.change_language') => $this->getLanguageCallbackHandler->handle($clientBotId, $chatId, $messageId),


            default => Log::warning('CallbackRouter: неизвестное действие', ['action' => $callbackData->action]),
        };
    }
}
