<?php

namespace App\Http\Controllers;

use App\Services\TelegramBotService\TelegramBotServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    protected TelegramBotServiceInterface $TelegramBotService;

    public function __construct(TelegramBotServiceInterface $TelegramBotService)
    {
        $this->TelegramBotService = $TelegramBotService;
    }

    public function handleWebhook(Request $request): void
    {
        $data = $request->json()->all();
        $this->TelegramBotService->getWebchook($data);
    }
}
