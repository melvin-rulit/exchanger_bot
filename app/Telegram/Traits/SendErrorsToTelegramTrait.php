<?php

namespace App\Telegram\Traits;

use Illuminate\Support\Facades\Http;

trait SendErrorsToTelegramTrait
{
    public function sendToTelegram(string $message, $context, $chanel): void
    {
        $token = config('services.error_notifier.bot_token');
        $chatId = config('services.error_notifier.chat_id');

        if (!$token || !$chatId) {
            return;
        }

        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        Http::post($url, [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ]);
    }
}
