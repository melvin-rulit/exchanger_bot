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
    public function getOrders(): JsonResponse
    {
        $orders = Order::with('user')->get();

        $prepare_orders = [];

        foreach ($orders as $order) {
            $media = $order->getMedia('amount_check');

            if ($media->count() > 0) {
                $media = $media->first();
                $order->media = $media->getUrl('screenshot');
            }
            $order->user;
            $order->client;
            $prepare_orders[] = $order;
        }

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
// TODO сделать событие если заказ завершен в чат с клиентом вывести /start чтоб он был на главном меню
        Client::where('id', $client['id'])
            ->update(['status' => $status]);

        $order = Order::findOrFail($request->selectedOrder['id']);
        $user = User::findOrFail($request->selectedUser['id']);

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
}
