<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Responses\ErrorResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\NotFoundResponse;
use App\Services\Web\Order\OrderService;
use App\Exceptions\TelegramApiException;
use App\Http\Requests\Order\GetOrderRequest;
use App\Http\Resources\Order\OrdersResource;
use App\Exceptions\User\UserNotFoundException;
use App\Http\Requests\Order\CloseOrderRequest;
use App\Http\Requests\Order\FixedOrderRequest;
use App\Services\ClientService\ClientsService;
use App\Exceptions\Order\OrderNotFoundException;
use App\Exceptions\Images\MediaLibraryException;
use App\Exceptions\Client\ClientNotFoundException;
use App\Exceptions\Order\OrdersNotFoundException;
use App\Http\Resources\Order\OrderMessagesResource;
use App\Http\Requests\Order\UpdateClientNameRequest;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Exceptions\Order\OrderClientNotFoundException;
use App\Http\Requests\Order\CreateOrderMessageRequest;
use App\Http\Requests\Order\SetOrderMessageReadRequest;
use App\Http\Requests\Order\UpdateClientCommentRequest;
use App\Services\TelegramBotService\TelegramMessageService;
use App\Http\Requests\Order\CreateOrderMessageWithPhotoRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class OrderController extends Controller
{
public function __construct(protected TelegramMessageService $telegramService, protected ClientsService $clientsService, protected OrderService $orderService){}

    /**
     * @throws OrdersNotFoundException
     */
    public function getOrders(): AnonymousResourceCollection
    {
        $orders = $this->orderService->getOrders();
        return OrdersResource::collection($orders);
    }

    public function getAllOrders(): AnonymousResourceCollection
    {
        $orders = $this->orderService->getAllOrders();
        return OrdersResource::collection($orders);
    }
    public function getOrdersWitchElasticSearch(Request $request): AnonymousResourceCollection
    {
        $orders = $this->orderService->getOrdersWitchElasticSearch($request);

        return OrdersResource::collection($orders);
    }
    public function getOrdersWitchSearch(Request $request): AnonymousResourceCollection
    {
        $orders = $this->orderService->getOrdersWitchSearch($request);

        return OrdersResource::collection($orders);
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

    public function storeMessageWithPhoto(CreateOrderMessageWithPhotoRequest $request): NotFoundResponse|ErrorResponse|SuccessResponse
    {
        try {
            $message = $this->orderService->storeMessageWithPhoto($request);
            return new SuccessResponse('Сообщение с фотографией сохранено', 'order', ['message' => new OrderMessagesResource($message)]);

        } catch (MediaLibraryException $e) {
            return new ErrorResponse('Не удалось сохранить изображение: ' . $e->getMessage());
        } catch (OrderNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }

    public function attachUserToOrder(Request $request): NotFoundResponse|SuccessResponse
    {
        try {
            $user = $this->orderService->attachUserToOrder($request);
            return new SuccessResponse('Менеджер закреплен', 'assigned_user', ['user' => $user]);

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

    public function endOrder(CloseOrderRequest $request): NotFoundResponse|SuccessResponse
    {
        try {
            $order = $this->orderService->endOrder($request);
            return new SuccessResponse('Заказ отменен.', 'update', ['order' => $order]);

        } catch (OrderNotFoundException|UserNotFoundException|ClientNotFoundException $e) {
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
