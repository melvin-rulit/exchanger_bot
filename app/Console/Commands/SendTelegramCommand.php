<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SendTelegramCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:send-command {cmd}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отправляет команду боту от имени пользователя';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $botToken = config('telegram.telegram_bot.token');
        $chatId = 1138241185;
        $command = $this->argument('cmd');

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        $response = Http::post($url, [
            'chat_id' => $chatId,
            'text' => "/{$command}",
            'parse_mode' => 'HTML'
        ]);

        if ($response->successful()) {
            $this->info("✅ Команда '/{$command}' отправлена боту!");
        } else {
            $this->error("❌ Ошибка: " . $response->body());
        }
    }
}
