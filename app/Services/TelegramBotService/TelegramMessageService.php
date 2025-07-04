<?php

namespace App\Services\TelegramBotService;

use Illuminate\Support\Facades\Http;
use App\Exceptions\TelegramApiException;
use Illuminate\Http\Client\ConnectionException;
use App\Exceptions\Helpers\InvalidStringValueException;

class TelegramMessageService
{
    protected string $url;

    /**
     * @throws InvalidStringValueException
     */
    public function __construct()
    {
        $this->url = ensure_string(config('telegram.telegram_bot.api_url'), 'telegram.telegram_bot.api_url') . ensure_string(config('telegram.telegram_bot.token'), 'telegram.telegram_bot.token');
    }

    /**
     * @throws TelegramApiException
     */
    public function sendMessage(int|string $chatId, string $message): void
    {
        if (empty($chatId) || empty($message)) {
            throw new \InvalidArgumentException('Chat ID и сообщение обязательны для отправки.');
        }

        $response = Http::post("{$this->url}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);

        if (!$response->successful()) {
            logger()->error('Ошибка отправки сообщения в Telegram', [
                'response' => $response->json(),
                'status' => $response->status(),
                'from' => 'TelegramMessageService'
            ]);

            throw new TelegramApiException('Ошибка отправки сообщения: ' . json_encode($response->json(), JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * @throws TelegramApiException|ConnectionException
     */
    public function sendPhoto(int|string $chatId, \CURLFile|string $photo, ?string $caption = null): void
    {
        $multipart = [
            [
                'name'     => 'chat_id',
                'contents' => $chatId,
            ],
            [
                'name'     => 'caption',
                'contents' => $caption ?? '',
            ],
        ];

        if ($photo instanceof \CURLFile) {
            $multipart[] = [
                'name'     => 'photo',
                'contents' => fopen($photo->getFilename(), 'r'),
                'filename' => $photo->getPostFilename(),
            ];
        } else {
            // URL
            $multipart[] = [
                'name'     => 'photo',
                'contents' => $photo,
            ];
        }

        $response = Http::attach(
            'photo',
            $photo instanceof \CURLFile ? fopen($photo->getFilename(), 'r') : null,
            $photo instanceof \CURLFile ? $photo->getPostFilename() : null
        )->post("{$this->url}/sendPhoto", [
            'chat_id' => $chatId,
            'caption' => $caption,
            'parse_mode' => 'HTML',
            'photo' => $photo instanceof \CURLFile ? null : $photo,
        ]);

        if (!$response->successful()) {
            logger()->error('Ошибка отправки фото в Telegram', [
                'response' => $response->json(),
                'status' => $response->status(),
            ]);

            throw new TelegramApiException('Ошибка отправки фото: ' . json_encode($response->json(), JSON_UNESCAPED_UNICODE));
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

        if (!$response->ok() || !$response->json('ok')) {
            throw new TelegramApiException('Ошибка отправки сообщения с кнопками: ' . json_encode($response->json(), JSON_UNESCAPED_UNICODE));
        }
    }

    public function sendDeleteReplay(int|string $chatId, string $text = '.'): void
    {
        $response = Http::post("{$this->url}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
            'reply_markup' => json_encode(['remove_keyboard' => true]),
            'parse_mode' => 'HTML',
        ]);
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

    /**
     * @throws TelegramApiException
     */
    public function deleteMessage(int|string $chatId, int $messageId): void
    {
//        $response = Http::post($this->url . "/deleteMessage", [
//            'chat_id' => $chatId,
//            'message_id' => $messageId,
//        ]);
//
//        if (!$response->successful()) {
//            throw new TelegramApiException('Ошибка удаления сообщения: ' . $response->body());
//        }
    }

    /**
     * @throws TelegramApiException
     */
    public function deleteMessages($chatId, $messageIdToDelete): void
    {
        $this->deleteMessage($chatId, $messageIdToDelete);

        // Удаляем сообщение с message_id - 1
        $this->deleteMessage($chatId, $messageIdToDelete - 1);
        $this->deleteMessage($chatId, $messageIdToDelete - 2);
        $this->deleteMessage($chatId, $messageIdToDelete - 3);
        $this->deleteMessage($chatId, $messageIdToDelete - 4);
        $this->deleteMessage($chatId, $messageIdToDelete - 5);
        $this->deleteMessage($chatId, $messageIdToDelete - 6);
        $this->deleteMessage($chatId, $messageIdToDelete - 7);
    }
}
