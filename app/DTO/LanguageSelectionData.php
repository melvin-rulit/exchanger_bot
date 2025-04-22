<?php

namespace App\DTO;

class LanguageSelectionData
{
    public function __construct(
        public int $chatId,
        public int $clientId,
        public int $messageId
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['chat_id'],
            $data['client_id'],
            $data['message_id'],
        );
    }
}
