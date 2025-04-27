<?php

namespace App\Services\TelegramBotService;

use Illuminate\Support\Facades\Http;
use App\Exceptions\TelegramApiException;

class TelegramMessageService
{

    protected string $url;

    public function __construct()
    {
        $this->url = ensure_string(config('telegram.telegram_bot.api_url')) . ensure_string(config('telegram.telegram_bot.token'));
    }

    /**
     * @throws TelegramApiException
     */
    public function sendMessage(int|string $chatId, string $message): void
    {
        $response = Http::post("{$this->url}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);

        if (!$response->successful()) {
            throw new TelegramApiException('Ошибка отправки сообщения: ' . $response->body());
        }
    }

    /**
     * @throws TelegramApiException
     */
    public function sendMessageWithButtons(int|string $chatId, string $message, $keyboard, int $messageId = null): void
    {
        $response = Http::post("{$this->url}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'reply_markup' => json_encode($keyboard),
            'parse_mode' => 'HTML',
        ]);

        if (!$response->successful()) {
            throw new TelegramApiException('Ошибка отправки сообщения с кнопками: ' . $response->body());
        }
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

    /**
     * @throws TelegramApiException
     */
    public function deleteMessage(int|string $chatId, int $messageId): void
    {
        $response = Http::post($this->url . "/deleteMessage", [
            'chat_id' => $chatId,
            'message_id' => $messageId,
        ]);

        if (!$response->successful()) {
            throw new TelegramApiException('Ошибка удаления сообщения: ' . $response->body());
        }
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
