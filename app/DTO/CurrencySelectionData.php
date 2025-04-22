<?php

namespace App\DTO;

class CurrencySelectionData
{
    public function __construct(
        public int $currencyId,
        public int $clientBotId,
        public int $chatId,
        public int $messageId
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['currency_id'],
            $data['client_bot_id'],
            $data['chat_id'],
            $data['message_id'],
        );
    }
}
