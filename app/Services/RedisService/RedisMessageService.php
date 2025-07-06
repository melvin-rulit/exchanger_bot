<?php

namespace App\Services\RedisService;

class RedisMessageService extends BaseService
{
    public function setDeletedMessageForChat(string|int $chatId, int $messageId): void
    {
        $this->rpush($chatId, $messageId);
    }

    public function getDeletedMessagesForChat(string|int $chatId): array
    {
        return $this->lrange($chatId);
    }

    public function forgetMessagesForDelete(string|int $chatId): void
    {
        $this->forget('messages', $chatId);
    }
}

