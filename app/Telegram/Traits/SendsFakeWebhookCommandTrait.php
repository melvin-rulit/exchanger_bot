<?php

namespace App\Telegram\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;

trait SendsFakeWebhookCommandTrait
{
    public function sendWebhookCommand($chatId, $command): string
    {
        $webhookUrl= '';
        Artisan::call('telegram:get-webhook-info');

        // Получаем вывод команды
        $output = Artisan::output();

        // Парсим JSON из строки (удаляем лишний текст)
        preg_match('/\{.*\}/s', $output, $matches);
        $jsonData = $matches[0] ?? null;

        if ($jsonData) {
            $webhookInfo = json_decode($jsonData, true);

            if (isset($webhookInfo['result']['url']) && !empty($webhookInfo['result']['url'])) {
                $webhookUrl = $webhookInfo['result']['url'];
            }
        }
        $fakeUpdate = [
            "update_id" => rand(100000000, 999999999),
            "message" => [
                "message_id" => rand(1, 10000),
                "from" => [
                    "id" => $chatId,
                    "is_bot" => false,
                    "first_name" => "User",
                    "username" => "test_user"
                ],
                "chat" => [
                    "id" => $chatId,
                    "first_name" => "User",
                    "username" => "test_user",
                    "type" => "private"
                ],
                "date" => time(),
                'text' => $command === 'start' ? "/$command" : $command,
            ]
        ];

        $response = Http::post($webhookUrl, $fakeUpdate);

        if ($response->successful()) {
            return "✅ Команда '/$command' отправлена через Webhook!";
        } else {
            return "❌ Ошибка: " . $response->body();
        }
    }
}
