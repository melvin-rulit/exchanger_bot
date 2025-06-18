<?php

namespace App\DTO;


class CallbackTelegramData extends BaseTelegramDTO
{
    public function __construct(
        public int|string $text,
        public array $photos,
        public array $document,
        public int $chatId,
        public int $clientBotId,
        public string $firsName,
        public string $userName,
        public bool $fromBot,
        public int $messageId,
        public ?string $callbackData = null
    ) {}

    public static function fromWebhook(array $data, bool $isCallback = false): self
    {
        $message = $data['message'] ?? [];
        $from = $isCallback ? ($data['from'] ?? []) : ($message['from'] ?? []);

        return new self(
            text: $message['text'] ?? '',
            photos: $message['photo'] ?? [],
            document: $message['document'] ?? [],
            chatId: $message['chat']['id'] ?? 0,
            clientBotId: $from['id'] ?? 0,
            firsName: $from['first_name'] ?? '',
            userName: $from['username'] ?? '',
            fromBot: $from['is_bot'] ?? false,
            messageId: $message['message_id'] ?? 0,
            callbackData: $data['data'] ?? null
        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
