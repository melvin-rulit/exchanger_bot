<?php

namespace App\Services\TelegramBotService;

interface TelegramBotServiceInterface
{
    /**
     * Обработка входящего webhook от Telegram
     *
     * @param array $data
     * @return void
     */
    public function getWebchook(array $data): void;
}
