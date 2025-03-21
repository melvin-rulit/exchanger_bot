<?php

use App\Http\Controllers\TelegramController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request;
});

Route::post('/webhook', [TelegramController::class, 'handleWebhook'])->name('webhook');
