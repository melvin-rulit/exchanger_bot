<?php

namespace App\DTO;

class CallbackTelegramData extends BaseDTO
{
    public int|string $text;
    public int $chatId;
    public int $clientBotId;
    public string $firsName;
    public string $userName;
    public bool $fromBot;
    public int $messageId;
    public ?string $callbackData = null;

    public static function fromWebhook(array $callbackQuery, bool $callbackQueryType = false): self
    {
        $data = $callbackQueryType
            ? [
                'text' => $callbackQuery['message']['text'],
                'chatId' => $callbackQuery['message']['chat']['id'],
                'clientBotId' => $callbackQuery['from']['id'],
                'firsName' => $callbackQuery['from']['first_name'],
                'userName' => $callbackQuery['from']['username'],
                'fromBot' => $callbackQuery['from']['is_bot'],
                'messageId' => $callbackQuery['message']['message_id'],
                'callbackData' => $callbackQuery['data'] ?? null,
            ]
            : [
                'text' => $callbackQuery['message']['text'],
                'chatId' => $callbackQuery['message']['chat']['id'],
                'clientBotId' => $callbackQuery['message']['from']['id'],
                'firsName' => $callbackQuery['message']['from']['first_name'],
                'userName' => $callbackQuery['message']['from']['username'],
                'fromBot' => $callbackQuery['message']['from']['is_bot'],
                'messageId' => $callbackQuery['message']['message_id'],
                'callbackData' => $callbackQuery['data'] ?? null,
            ];

        return self::fromArray($data);
    }
}
