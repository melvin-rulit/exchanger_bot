<?php

namespace App\DTO;

class BankSelectionData
{
    public function __construct(
        public int $bankId,
        public int $clientBotId,
        public int $chatId,
        public int $messageId
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['bank_id'],
            $data['client_bot_id'],
            $data['chat_id'],
            $data['message_id']
        );
    }
}
