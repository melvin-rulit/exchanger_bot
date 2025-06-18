<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\CunsultationController;
use App\Http\Controllers\TemplateMessageController;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Order/Index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/adminOrders', function () {
    return Inertia::render('Order/Index');
})->middleware(['auth', 'verified'])->name('adminOrders');

Route::get('/consultation', function () {
    return Inertia::render('Consultation/Index');
})->middleware(['auth', 'verified'])->name('consultation');

Route::get('/settings', function () {
    return Inertia::render('Settings/Index');
})->middleware(['auth', 'verified'])->name('settings');

Route::group(['prefix' => 'admin'], function () {
    Route::get('/users', function () {
        return Inertia::render('Admin/Index');
    })->middleware(['auth', 'verified'])->name('adminUsers');
});

Route::middleware('auth')->group(function () {

    Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
        Route::get('/', [OrderController::class, 'getOrders']);
        Route::patch('/set_read_messages/{orderId}', [OrderController::class, 'setMessagesOrderRead'])->whereNumber('orderId');
        Route::get('/get_order/{orderId}', [OrderController::class, 'getOrder'])->whereNumber('orderId');
        Route::post('/send_photo/{orderId}', [OrderController::class, 'storeMessageWithPhoto'])->whereNumber('orderId');
        Route::post('/send_message/{orderId}', [OrderController::class, 'storeMessage'])->whereNumber('orderId');
        Route::put('/assign_executor', [OrderController::class, 'attachUserToOrder']);
        Route::put('/close_order', [OrderController::class, 'closeOrder']);
        Route::put('/fix_order/{orderId}', [OrderController::class, 'fixOrder'])->whereNumber('orderId');
        Route::patch('/update_client_name/{orderId}', [OrderController::class, 'updateClientName'])->whereNumber('orderId');
        Route::patch('/status/{orderId}', [OrderController::class, 'updateStatus'])->whereNumber('orderId');
    });

    Route::group(['prefix' => 'consultation', 'as' => 'consultation.'], function () {
        Route::get('/messages', [CunsultationController::class, 'getMessages']);
        Route::get('/today_messages/{message_id}', [CunsultationController::class, 'getTodayMessages']);
        Route::post('/send_message/{messageId}', [CunsultationController::class, 'storeMessage'])->whereNumber('messageId');
        Route::post('/send_photo', [CunsultationController::class, 'storeMessageWithPhoto']);
        Route::patch('/set_read_messages/{messageId}', [CunsultationController::class, 'setMessagesConsultantRead'])->whereNumber('messageId');
    });

    Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
        Route::get('/', [UserController::class, 'getUsers']);
        Route::get('/managers', [UserController::class, 'getManagers']);
        Route::get('/me', [UserController::class, 'getAuthUser']);
        Route::patch('/current_user/update/{userId}', [UserController::class, 'updateUser'])->whereNumber('userId');
        Route::patch('/locked', [UserController::class, 'lockScreen']);
        Route::patch('/toggle/notification', [UserController::class, 'lockScreen']);
        Route::patch('/unlocked/send_password', [UserController::class, 'sendPasswordForUnlock']);
        Route::patch('/locked/set_password', [UserController::class, 'setPasswordForLock']);
        Route::get('/pined/chat', [UserController::class, 'getPinnedChat']);
        Route::post('/pin/chat', [UserController::class, 'pinChat']);
        Route::patch('/un_pin/chat', [UserController::class, 'unPinChat']);
        Route::patch('/toggle/notification', [UserController::class, 'toggleNotification']);
        Route::post('/send_photo', [UserController::class, 'storePhoto']);
    });

    Route::group(['prefix' => 'template', 'as' => 'templates.'], function () {
        Route::get('/', [TemplateMessageController::class, 'getTemplates']);
        Route::post('/message/add', [TemplateMessageController::class, 'storeTemplate']);
        Route::patch('/message/update/{templateId}', [TemplateMessageController::class, 'updateTemplate'])->whereNumber('templateId');
        Route::delete('/message/{templateId}', [TemplateMessageController::class, 'deleteTemplate'])->whereNumber('templateId');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/get-webhook-info', [TelegramController::class, 'getWebhookInfo']);
});

require __DIR__.'/auth.php';
