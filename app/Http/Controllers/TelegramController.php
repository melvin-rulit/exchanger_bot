<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use App\Services\TelegramBotService\TelegramBotServiceInterface;



class TelegramController extends Controller
{
    public function __construct(protected TelegramBotServiceInterface $telegramBotService){}

    public function handleWebhook(Request $request): void
    {
        $this->telegramBotService->getWebchook($request->all());
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
