<?php

namespace App\Console\Commands\EncryptDescrypt;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;

class EncryptKeyCommand extends Command
{
    protected $signature = 'key:encrypt {key} {--save-env} {--env-key=SECRET_KEY}';
    protected $description = 'Шифрует переданный ключ, выводит в консоль и опционально сохраняет в .env';

    public function handle(): int
    {
        $key = $this->argument('key');
        $encryptedKey = Crypt::encryptString($key);

//        // Логируем
//        Log::info('🔒 Зашифрованный ключ:', [
//            'original' => $key,
//            'encrypted' => $encryptedKey,
//        ]);

        // Выводим в консоль
        $this->info('✅ Оригинальный ключ: ' . $key);
        $this->info('🔒 Зашифрованный ключ: ' . $encryptedKey);

        // Сохраняем в .env если нужно
        if ($this->option('save-env')) {
            $envKey = $this->option('env-key');
            $this->saveToEnv($envKey, $encryptedKey);
            $this->info("✅ Ключ сохранён в .env как {$envKey}");
        }

        return self::SUCCESS;
    }

    private function saveToEnv(string $key, string $value): void
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            $this->error('Файл .env не найден!');
            return;
        }

        $content = file_get_contents($envPath);

        if (preg_match("/^{$key}=.*/m", $content)) {
            // Если ключ уже есть — заменяем его
            $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
        } else {
            // Иначе добавляем новый
            $content .= "\n{$key}={$value}\n";
        }

        file_put_contents($envPath, $content);
    }
}
