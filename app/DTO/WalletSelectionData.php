<?php

namespace App\DTO;

class WalletSelectionData
{
    public function __construct(
        public int $clientId,
        public int $chatId,
        public int $messageId
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['client_id'],
            $data['chat_id'],
            $data['message_id'],
        );
    }
}
