<?php

namespace App\Console\Commands\Webhook;

use App\Exceptions\TelegramApiException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DeleteWebhook extends Command
{
    protected $signature = 'telegram:delete-webhook {token)}';
    protected $description = 'Удаляет вебхук у Telegram бота';

    /**
     * @throws TelegramApiException
     */
    public function handle(): int
    {
        $token = $this->argument('token)');
//        $token = config('telegram.bot_token');

//        if (empty($token)) {
//            $this->error('❌ Не указан Telegram Bot Token в config/telegram.php или в .env');
//            return self::FAILURE;
//        }

        $url = "https://api.telegram.org/bot{$token}/deleteWebhook";

        $response = Http::post($url);

        if (!$response->ok() || !$response->json('ok')) {
            logger()->error('Ошибка удаления вебхука', [
                'response' => $response->json(),
            ]);

            throw new TelegramApiException('Не удалось удалить вебхук: ' . json_encode($response->json(), JSON_UNESCAPED_UNICODE));
        }

        $this->info('✅ Вебхук успешно удалён!');
        return self::SUCCESS;
    }
}
