<?php

namespace App\Telegram\Traits;

use Illuminate\Support\Facades\Http;
use App\Exceptions\AppConfig\ConfigNotFoundException;

trait SendErrorsToTelegramTrait
{
    /**
     * @throws ConfigNotFoundException
     */
    public function sendToTelegram(string $message, ?array $context): void
    {
        $token = ensure_string(config('telegram.error_notifier_bot.token'));
        $chatId = ensure_string(config('telegram.error_notifier_bot.chat_id'));

        if (!$token || !$chatId) {
           throw new ConfigNotFoundException('Не назначены настройки', 404, null, ['file' => 'SendErrorsToTelegramTrait']);
        }

        $url = ensure_string(config('telegram.telegram_bot.api_url')) . ensure_string(config('telegram.error_notifier_bot.token')).'/sendMessage';

        Http::post($url, [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ]);
    }
}
