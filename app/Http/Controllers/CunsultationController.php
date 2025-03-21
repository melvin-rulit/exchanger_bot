<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Services\TelegramBotService\TelegramMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CunsultationController extends Controller
{
    protected TelegramMessageService $telegramService;

    public function __construct(TelegramMessageService $telegramService)
    {
        $this->telegramService = $telegramService;
    }
    public function getMessages(): JsonResponse
    {
        $messages = Message::getTodayMessagesOnlyForConsultation();

        return new JsonResponse([
            'messages' => $messages
        ]);
    }
}
