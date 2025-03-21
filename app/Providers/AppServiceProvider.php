<?php

namespace App\Providers;

use App\Services\ClientService\ClientServiceInterface;
use App\Services\ClientService\ClientsService;
use App\Services\TelegramBotService\TelegramBotService;
use App\Services\TelegramBotService\TelegramBotServiceInterface;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TelegramBotServiceInterface::class, TelegramBotService::class);
        $this->app->bind(ClientServiceInterface::class, ClientsService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
