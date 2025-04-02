<?php

namespace App\Http\Controllers;

use App\Http\Requests\Consultation\CreateConsultantMessageRequest;
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
        $message = Message::getTodayMessagesOnlyForConsultationByGroup();

        return new JsonResponse([
            'last_message' => $message
        ]);
    }
    public function getTodayMessages(int $message_id): JsonResponse
    {
        $chat_id = Message::getChatId($message_id);
        $messages = Message::getTodayMessagesOnlyForConsultation($chat_id);

        return new JsonResponse([
            'messages' => $messages
        ]);
    }
    public function storeMessage(CreateConsultantMessageRequest  $request, $message_id): JsonResponse
    {
        $chat_id = Message::getChatId($message_id);

        $created_message = Message::create([
            'chat_id' => $chat_id,
            'order_id' => null,
            'user_id' => auth()->user()->id,
            'sender_type' => 'user',
            'message' => $request->getMessage(),
        ]);

        $this->telegramService->sendMessage($chat_id, $request->getMessage());

        return response()->json($created_message);
    }
    public function setMessagesOrderRead($message_id): void
    {
        $message = Message::find($message_id);

        $message->is_message = false;
        $message->save();
    }
}
