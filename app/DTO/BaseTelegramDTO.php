<?php

namespace App\DTO;

use App\Telegram\Traits\ToArrayTrait;
use App\DTO\Contracts\TelegramDataInterface;

abstract class BaseTelegramDTO implements TelegramDataInterface
{
    use ToArrayTrait;

    abstract public static function fromWebhook(array $data): self;
}
