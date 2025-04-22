<?php

namespace App\DTO;

class CountrySelectionData
{
    public function __construct(
        public string $countryCode,
        public int $clientBotId,
        public int $chatId,
        public int $messageId
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['country_code'],
            $data['client_bot_id'],
            $data['chat_id'],
            $data['message_id'],
        );
    }
}
