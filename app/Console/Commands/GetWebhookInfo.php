<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetWebhookInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:get-webhook-info';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Получает информацию о текущем вебхуке Telegram-бота';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $botToken = config('telegram.telegram_bot.token');
        $url = "https://api.telegram.org/bot{$botToken}/getWebhookInfo";

        $response = Http::get($url);

        if ($response->successful()) {
            $webhookInfo = $response->json();

            if (empty($webhookInfo['result']['url'])) {
                $this->warn("⚠️ Вебхук НЕ установлен!");
            } else {
                $this->info("✅ Вебхук установлен на URL: " . $webhookInfo['result']['url']);
            }

            $this->info("📊 Полная информация о вебхуке:");
            $this->line(json_encode($webhookInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->error("❌ Ошибка: " . $response->body());
        }
    }
}
