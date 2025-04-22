<?php

namespace App\DTO\Contracts;

interface TelegramDataInterface extends ArrayableDTO
{
    public static function fromWebhook(array $data): self;
}
