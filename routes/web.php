<?php

use App\Http\Controllers\CunsultationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Order/Index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/consultation', function () {
    return Inertia::render('Consultation/Index');
})->middleware(['auth', 'verified'])->name('consultation');

Route::middleware('auth')->group(function () {

    Route::group(['prefix' => 'orders', 'name' => 'orders'], function () {
        Route::get('/', [OrderController::class, 'getOrders']);
        Route::patch('/{id}', [OrderController::class, 'setOrderMessage']);
        Route::patch('/set_read_messages/{id}', [OrderController::class, 'setMessagesOrderRead']);
        Route::patch('/get_order/{id}', [OrderController::class, 'getOrder'])->whereNumber('id');
        Route::post('/send_message/{order}', [OrderController::class, 'storeMessage']);
        Route::put('/update_order', [OrderController::class, 'updateOrder']);
        Route::put('/close_order', [OrderController::class, 'closeOrder']);
    });

    Route::group(['prefix' => 'consultation', 'name' => 'consultation'], function () {
        Route::get('/get_messages', [CunsultationController::class, 'getMessages']);
    });

    Route::group(['prefix' => 'users', 'name' => 'users'], function () {
        Route::get('/', [UserController::class, 'getUsers']);
        Route::get('/me', [UserController::class, 'getAuthUser']);
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
