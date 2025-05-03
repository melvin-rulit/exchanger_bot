<?php

namespace App\Services\RedisService;

use Illuminate\Support\Facades\Redis;

abstract class BaseService
{
    protected int $defaultTtl = 3600; // TTL по умолчанию: 1 час

    // Генерация ключа
    protected function key(string $prefix, int|string $chatId): string
    {
        return "{$prefix}:{$chatId}";
    }

    // Установка значения с TTL
    public function set(string $prefix, int $chatId, string|int|bool $value, ?int $ttl = null): void
    {
        if ($ttl === 0) {
            Redis::set($this->key($prefix, $chatId), $value);
        } else {
            $ttl = $ttl ?? $this->defaultTtl;
            Redis::setex($this->key($prefix, $chatId), $ttl, $value);
        }
    }

    public function get(string $prefix, int $chatId): ?string
    {
        return Redis::get($this->key($prefix, $chatId));
    }
    public function forget(string $prefix, int|string $chatId): void
    {
        Redis::del($this->key($prefix, $chatId));
    }
    public function has(string $prefix, int $chatId): bool
    {
        return Redis::exists($this->key($prefix, $chatId)) === 1;
    }
}
