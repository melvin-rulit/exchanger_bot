<?php

namespace App\Telegram\Traits;

trait ToArrayTrait
{
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
