<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SetTelegramCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-commands';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Устанавливает команды для Telegram-бота';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $botToken = config('telegram.telegram_bot.token');
        $url = "https://api.telegram.org/bot{$botToken}/setMyCommands";

        $commands = [
            ['command' => 'start', 'description' => 'На главную'],
//            ['command' => 'help', 'description' => 'Список команд'],
//            ['command' => 'profile', 'description' => 'Ваш профиль'],
//            ['command' => 'settings', 'description' => 'Настройки']
        ];

        $response = Http::post($url, ['commands' => $commands]);

        if ($response->successful()) {
            $this->info("✅ Команды успешно установлены!");
        } else {
            $this->error("❌ Ошибка: " . $response->body());
        }
    }
}
