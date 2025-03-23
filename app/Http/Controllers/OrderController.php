<?php

namespace App\Http\Controllers;

use App\Events\OrderUpdated;
use App\Http\Requests\Order\CreateOrderMessageRequest;
use App\Http\Resources\OrderMessagesResource;
use App\Models\Message;
use App\Models\Order;
use App\Models\User;
use App\Services\TelegramBotService\TelegramMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class OrderController extends Controller
{
    protected TelegramMessageService $telegramService;

    public function __construct(TelegramMessageService $telegramService)
    {
        $this->telegramService = $telegramService;
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
//        Order::where('id', $request->selectedOrder['id'])
//            ->update(['user_id' => $request->selectedUser['id']]);
//
//        return response()->json(['message' => 'Заказ обновлён']);

        $order = Order::findOrFail($request->selectedOrder['id']);
        $user = User::findOrFail($request->selectedUser['id']);

        $order->status = 'success';
        $order->save();

        return response()->json(['message' => 'Заказ завершен', 'order' => $order]);
    }
}
