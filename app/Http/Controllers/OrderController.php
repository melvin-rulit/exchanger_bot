<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Events\OrderUpdated;
use Illuminate\Http\JsonResponse;
use App\Http\Responses\ErrorResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\NotFoundResponse;
use App\Exceptions\TelegramApiException;
use App\Services\Web\Order\OrderService;
use App\Http\Requests\Order\GetOrderRequest;
use App\Services\ClientService\ClientsService;
use App\Exceptions\User\UserNotFoundException;
use App\Http\Requests\Order\CloseOrderRequest;
use App\Http\Requests\Order\FixedOrderRequest;
use App\Exceptions\Order\OrderNotFoundException;
use App\Exceptions\Order\OrdersNotFoundException;
use App\Exceptions\Client\ClientNotFoundException;
use App\Http\Resources\Order\OrderMessagesResource;
use App\Http\Requests\Order\UpdateClientNameRequest;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Exceptions\Order\OrderClientNotFoundException;
use App\Http\Requests\Order\CreateOrderMessageRequest;
use App\Http\Requests\Order\SetOrderMessageReadRequest;
use App\Services\TelegramBotService\TelegramMessageService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class OrderController extends Controller
{
public function __construct(protected TelegramMessageService $telegramService, protected ClientsService $clientsService, protected OrderService $orderService){}

    public function getOrders(): NotFoundResponse|AnonymousResourceCollection
    {
        try {
            return $this->orderService->getOrders();

        } catch (OrdersNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }
    public function getOrder(GetOrderRequest $request): JsonResponse|AnonymousResourceCollection
    {
        try {
            $order = $this->orderService->getOrder($request);
            return OrderMessagesResource::collection($order->messages);

        } catch (OrderNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }
    public function setOrderMessage($id): JsonResponse
    {
        $order = Order::find($id);

        $order->is_message = !$order->is_message;
        $order->save();

        broadcast(new OrderUpdated($order));

        return response()->json($order);
    }

    public function setMessagesOrderRead(SetOrderMessageReadRequest $request): SuccessResponse|NotFoundResponse
    {
        try {
            $this->orderService->setMessagesRead($request);
            return new SuccessResponse('Сообщение отмечено прочитанным');

        } catch (OrderNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }

    public function storeMessage(CreateOrderMessageRequest $request): NotFoundResponse|ErrorResponse|SuccessResponse
    {
        try {
            $message = $this->orderService->storeMessage($request);
            return new SuccessResponse('Сообщение сохранено', 'order', ['message' => new OrderMessagesResource($message)]);

        } catch (TelegramApiException $e) {
            return new ErrorResponse($e->getMessage());
        } catch (OrderNotFoundException|OrderClientNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }

    public function attachUserToOrder(Request $request): NotFoundResponse|SuccessResponse
    {
        try {
            $user = $this->orderService->attachUserToOrder($request);
            return new SuccessResponse('Менеджер закреплен', 'assigned_user', ['user_id' => $user->id]);

        } catch (UserNotFoundException|OrderNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }

    public function updateStatus(UpdateOrderStatusRequest $request): NotFoundResponse|SuccessResponse
    {
        try {
            $status = $this->orderService->changeStatus($request);
            return new SuccessResponse('Статус обновлен', 'order', ['status' => $status]);

        } catch (OrderNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }

    public function closeOrder(CloseOrderRequest $request): NotFoundResponse|SuccessResponse
    {
        try {
            $order = $this->orderService->closeOrder($request);
            return new SuccessResponse('Заказ завершен.', 'update', ['order' => $order]);

        } catch (OrderNotFoundException|UserNotFoundException|ClientNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }

    public function updateClientName(UpdateClientNameRequest $request): NotFoundResponse|SuccessResponse
    {
        try {
            $this->orderService->updateClientName($request);
            return new SuccessResponse('Имя клиента успешно обновлено.');

        } catch (OrderClientNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }
    public function fixOrder(FixedOrderRequest $request): NotFoundResponse|SuccessResponse
    {
        try {
            $order = $this->orderService->fixOrder($request);
            return new SuccessResponse($order->is_pinned ? 'Заказ закреплен' : 'Закрепление снято', 'order', ['fixed' => $order]);

        } catch (OrderNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }

}
