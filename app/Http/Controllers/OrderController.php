<?php

namespace App\Http\Controllers;

use App\Events\OrderUpdated;
use App\Http\Requests\Order\CreateOrderMessageRequest;
use App\Http\Resources\OrderMessagesResource;
use App\Models\Client;
use App\Models\Message;
use App\Models\Order;
use App\Models\User;
use App\Services\ClientService\ClientsService;
use App\Services\TelegramBotService\TelegramMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class OrderController extends Controller
{
    protected TelegramMessageService $telegramService;
    protected ClientsService $clientsService;

    public function __construct(TelegramMessageService $telegramService, ClientsService $clientsService)
    {
        $this->telegramService = $telegramService;
        $this->clientsService = $clientsService;
    }
//    public function getOrders(): JsonResponse
//    {
//        $orders = Order::with('user')->get();
//
//        $prepare_orders = [];
//
//        foreach ($orders as $order) {
//            $media = $order->getMedia('amount_check');
//
//            if ($media->count() > 0) {
//                $media = $media->first();
//                $order->media = $media->getUrl('screenshot');
//            }
//            $order->user;
//            $order->client;
//            $prepare_orders[] = $order;
//        }
//
//        return new JsonResponse([
//            'orders' => $prepare_orders
//        ]);
//    }

//    public function getOrders(): JsonResponse
//    {
//        $orders = Order::with(['user', 'client'])->get(); // Загружаем сразу user и client
//
//        $prepare_orders = $orders->map(function ($order) {
//            $media = $order->getMedia('amount_check');
//
//            if ($media->count() > 0) {
//                $media = $media->first();
//                $order->media = $media->getUrl('screenshot');
//            }
//
//            return $order;
//        });
//
//        // Сортируем так, чтобы "success" был в конце
//        $sorted_orders = $prepare_orders->sortBy(function ($order) {
//            return $order->status === 'success' ? 1 : 0;
//        })->values(); // `values()` сбрасывает ключи массива
//
//        return new JsonResponse([
//            'orders' => $sorted_orders
//        ]);
//    }

    public function getOrders(): JsonResponse
    {
        $orders = Order::with(['user', 'client'])
            ->orderByRaw("FIELD(status, 'new', 'active', 'success')")
            ->get();

        $prepare_orders = $orders->map(function ($order) {
            $media = $order->getMedia('amount_check');

            if ($media->count() > 0) {
                $media = $media->first();
                $order->media = $media->getUrl('screenshot');
            }

            return $order;
        });

        return new JsonResponse([
            'orders' => $prepare_orders
        ]);
    }
    public function getOrder(int $id, Request $request): JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
      $order = order::with('messages')->where('id', $id)->first();

        if ($order) {
            $order->is_message = false;
            $order->save();

            return OrderMessagesResource::collection($order->messages);
        }

        return response()->json(['error' => 'Messages not found'], 404);
    }
    public function setOrderMessage($id): JsonResponse
    {
        $order = Order::find($id);

        // Изменяем значение поля is_message
        $order->is_message = !$order->is_message;
        $order->save();

        broadcast(new OrderUpdated($order));

        return response()->json($order);
    }
    public function setMessagesOrderRead($id): void
    {
        $order = Order::find($id);

        $order->is_message = false;
        $order->save();
    }

    public function storeMessage(CreateOrderMessageRequest  $request, $orderId)
    {
        $order = Order::find($orderId);

        $created_message = Message::create([
            'chat_id' => $order->chat_id,
            'order_id' => $orderId,
            'user_id' => auth()->user()->id,
            'sender_type' => 'user',
            'message' => $request->getMessage(),
        ]);

        $this->telegramService->sendMessage($order->chat_id, $request->getMessage());

        return response()->json($created_message);
    }

    public function updateOrder(Request $request): JsonResponse
    {
        $order = Order::findOrFail($request->selectedOrder['id']);
        $user = User::findOrFail($request->selectedUser['id']);

        $order->user()->associate($user);
        $order->status = 'active';
        $order->save();

        return response()->json(['message' => 'Заказ обновлён', 'order' => $order]);
    }

    public function closeOrder(Request $request): JsonResponse
    {
        $client = $request->selectedOrder['client'];
        $language = $this->clientsService->getClientLanguage($client['bot_id']);
        $status = '';

        switch ($language) {
            case 'ru':
                $status = 'На главную';
                break;
            case 'en':
                $status = 'to_main';
                break;
        }

        Client::where('id', $client['id'])
            ->update(['status' => $status]);

        $order = Order::findOrFail($request->selectedOrder['id']);
        $user = User::findOrFail($request->selectedUser['id']);

        $this->sendWebhookUpdate($order->chat_id, 'start');

        $order->status = 'success';
        $order->save();

        return response()->json(['message' => 'Заказ завершен', 'order' => $order]);
    }
    public function fixOrder(int $orderId): JsonResponse
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Заказ не найден'], 404);
        }

        $order->update(['is_pinned' => !$order->is_pinned]);

        return response()->json([
            'message' => $order->is_pinned ? 'Заказ закреплен' : 'Закрепление снято',
            'order' => $order
        ]);
    }

    public function sendWebhookUpdate($chatId, $command): string
    {
        $webhookUrl= '';
        Artisan::call('telegram:get-webhook-info');

        // Получаем вывод команды
        $output = Artisan::output();

        // Парсим JSON из строки (удаляем лишний текст)
        preg_match('/\{.*\}/s', $output, $matches);
        $jsonData = $matches[0] ?? null;

        if ($jsonData) {
            $webhookInfo = json_decode($jsonData, true);

            if (isset($webhookInfo['result']['url']) && !empty($webhookInfo['result']['url'])) {
                $webhookUrl = $webhookInfo['result']['url'];
            }
        }
        $fakeUpdate = [
            "update_id" => rand(100000000, 999999999),
            "message" => [
                "message_id" => rand(1, 10000),
                "from" => [
                    "id" => $chatId,
                    "is_bot" => false,
                    "first_name" => "User",
                    "username" => "test_user"
                ],
                "chat" => [
                    "id" => $chatId,
                    "first_name" => "User",
                    "username" => "test_user",
                    "type" => "private"
                ],
                "date" => time(),
                "text" => "/$command"
            ]
        ];

        $response = Http::post($webhookUrl, $fakeUpdate);

        if ($response->successful()) {
            return "✅ Команда '/$command' отправлена через Webhook!";
        } else {
            return "❌ Ошибка: " . $response->body();
        }
    }

}
