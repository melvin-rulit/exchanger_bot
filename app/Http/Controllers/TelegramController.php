<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use App\Services\TelegramBotService\TelegramBotServiceInterface;



class TelegramController extends Controller
{
    public function __construct(protected TelegramBotServiceInterface $telegramBotService){}

    public function handleWebhook(Request $request): JsonResponse
    {
        try {
            $this->telegramBotService->getWebchook($request->all());
        } catch (\Throwable $e) {
            \Log::error('[Telegram webhook ERROR]', [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => collect($e->getTrace())->take(10),
                'payload' => $request->all(),
            ]);
        }

        return response()->json(['ok' => true]);  // Обязательно вернуть HTTP ответ!
    }


    public function getWebhookInfo(): JsonResponse
    {
        // Запускаем artisan-команду и получаем результат
        Artisan::call('telegram:get-webhook-info');

        $output = Artisan::output();

        return response()->json([
            'status' => 'success',
            'message' => $output
        ]);
    }
}
