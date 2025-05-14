<?php

namespace App\DTO;

class RequisiteSelectionData
{
    public function __construct(
        public int $chatId,
        public int $clientBotId,
        public int $messageId
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['chat_id'],
            $data['client_bot_id'],
            $data['message_id'],
        );
    }
}
