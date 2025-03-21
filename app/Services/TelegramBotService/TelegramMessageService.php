<?php

namespace App\Services\TelegramBotService;

use Illuminate\Support\Facades\Http;

class TelegramMessageService
{

    protected string $url;

    public function __construct()
    {
        $this->url = config('telegram.telegram_bot.api_url') . config('telegram.telegram_bot.token');
    }

    public function sendMessage(int|string $chatId, string $message): bool
    {
        $response = Http::post("{$this->url}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);

        return $response->successful();
    }
}
