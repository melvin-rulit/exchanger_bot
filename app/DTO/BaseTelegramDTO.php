<?php

namespace App\DTO;

use App\Telegram\Traits\SerializeTrait;
use App\DTO\Contracts\TelegramDataInterface;
use JsonSerializable;

abstract class BaseTelegramDTO implements TelegramDataInterface, JsonSerializable
{
    use SerializeTrait;

    abstract public static function fromWebhook(array $data): self;
}
