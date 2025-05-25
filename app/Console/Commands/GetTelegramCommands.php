<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetTelegramCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:get-commands';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Получает список команд Telegram-бота';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $botToken = config('telegram.telegram_bot.token');
        $url = "https://api.telegram.org/bot{$botToken}/getMyCommands";

        $response = Http::get($url);

        if ($response->successful()) {
            $commands = $response->json()['result'];

            if (empty($commands)) {
                $this->warn("⚠️ Команды не установлены.");
                return;
            }

            $this->info("➡ Установленные команды Telegram:");

            foreach ($commands as $command) {
                $this->line(" /" . $command['command'] . " - " . $command['description']);
            }
        } else {
            $this->error("❌ Ошибка: " . $response->body());
        }
    }
}
