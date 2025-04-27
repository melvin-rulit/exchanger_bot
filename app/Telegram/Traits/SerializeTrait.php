<?php

namespace App\Telegram\Traits;

use JsonException;

trait SerializeTrait
{
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * @throws JsonException
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options | JSON_THROW_ON_ERROR);
    }
}
