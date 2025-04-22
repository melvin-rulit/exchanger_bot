<?php

namespace App\Services\TelegramBotService;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
    public function sendMessageWithButtons(int|string $chatId, string $message, $keyboard, int $messageId = null): bool
    {
        $response = Http::post("{$this->url}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'reply_markup' => json_encode($keyboard),
            'parse_mode' => 'HTML',
        ]);

        return $response->successful();
    }

    public function editMessage(int|string $chatId, int $messageId, string $message, ?array $keyboard = null): bool
    {
        $payload = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ];

        if ($keyboard) {
            $payload['reply_markup'] = json_encode($keyboard);
        }

        $response = Http::post($this->url . 'editMessageText', $payload);

        return $response->successful();
    }

    public function sendPhoto(int|string $chatId, string $photoUrl, ?string $caption = null, ?array $keyboard = null): bool
    {
        $payload = [
            'chat_id' => $chatId,
            'photo' => $photoUrl,
        ];

        if ($caption) {
            $payload['caption'] = $caption;
            $payload['parse_mode'] = 'HTML';
        }

        if ($keyboard) {
            $payload['reply_markup'] = json_encode($keyboard);
        }

        $response = Http::post($this->url . 'sendPhoto', $payload);

        return $response->successful();
    }

    public function deleteMessage(int|string $chatId, int $messageId): bool
    {
        $response = Http::post($this->url . "/deleteMessage", [
            'chat_id' => $chatId,
            'message_id' => $messageId,
        ]);

        return $response->successful();
    }

    public function answerCallbackQuery(string $callbackQueryId, ?string $text = null): bool
    {
        $payload = [
            'callback_query_id' => $callbackQueryId,
        ];

        if ($text) {
            $payload['text'] = $text;
            $payload['show_alert'] = false;
        }

        $response = Http::post($this->url . 'answerCallbackQuery', $payload);

        return $response->successful();
    }
}
