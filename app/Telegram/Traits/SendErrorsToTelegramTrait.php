<?php

namespace App\Telegram\Traits;

use Illuminate\Support\Facades\Http;
use App\Exceptions\Helpers\InvalidStringValueException;

trait SendErrorsToTelegramTrait
{
    /**
     * @throws InvalidStringValueException
     */
    public function sendToTelegram(string $message, ?array $context): void
    {
        $chatId = ensure_string(config('telegram.error_notifier_bot.chat_id'), 'telegram.error_notifier_bot.chat_id');

        $url = ensure_string(config('telegram.telegram_bot.api_url'), 'telegram.telegram_bot.api_url')
            . ensure_string(config('telegram.error_notifier_bot.token'), 'telegram.error_notifier_bot.token')
            . '/sendMessage';

        Http::post($url, [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ]);
    }
}
