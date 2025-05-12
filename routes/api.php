<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\TelegramController;

Route::get('/user', function (Request $request) {
    return $request;
});

Route::post('/webhook', [TelegramController::class, 'handleWebhook'])->name('webhook');
Route::post('/export/telegram_service', [ExportController::class, 'exportTelegramService']);
