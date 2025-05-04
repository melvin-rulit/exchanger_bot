<?php

namespace App\Services\RedisService;

use App\Enums\ModelEnum;

class RedisFieldService extends BaseService
{
    // === ORDER ===

    public function setOrderId(int $chatId, int $orderId, ?int $ttl = null): void
    {
        $this->set(ModelEnum::ORDERID->value, $chatId, (string) $orderId, $ttl);
    }
    public function getOrderId(string|int $chatId): ?int
    {
        $value = $this->get(ModelEnum::ORDERID->value, $chatId);
        return $value !== null ? (int) $value : null;
    }

}

