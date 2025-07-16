<?php

namespace App\Services\Web\Order;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Client;
use App\Models\Message;
use App\Telegram\Traits\HandlesFile;
use App\Services\Web\BaseWebService;
use App\Exceptions\TelegramApiException;
use App\Exceptions\User\UserNotFoundException;
use App\Services\ClientService\ClientsService;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\Order\OrderNotFoundException;
use App\Exceptions\Images\MediaLibraryException;
use App\Exceptions\Order\OrdersNotFoundException;
use App\Services\RedisService\RedisSessionService;
use App\Exceptions\Client\ClientNotFoundException;
use App\Telegram\Traits\SendsFakeWebhookCommandTrait;
use App\Exceptions\Order\OrderClientNotFoundException;
use App\Services\TelegramBotService\TelegramMessageService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class OrderService extends BaseWebService
{
    use SendsFakeWebhookCommandTrait, HandlesFile;

    public function __construct(RedisSessionService $redis, TelegramMessageService $telegramMessageService, ClientsService $clientsService, protected ClientTransferService $clientTransferService)
    {
        parent::__construct($redis, $telegramMessageService, $clientsService);
    }

    public function getAllOrders(): LengthAwarePaginator
    {
        return Order::with(['user', 'client', 'pinnedMessages', 'bank', 'lastMessage'])->paginate(16);
    }
    /**
     * @throws OrdersNotFoundException
     */
    public function getOrders(): LengthAwarePaginator
    {
        $query = Order::with(['user', 'client', 'pinnedMessages', 'bank', 'lastMessage'])
            ->whereDate('created_at', Carbon::today());

        $user = Auth::user();

        if (! $user->hasRole('Администратор')) {
            $query->where(function ($query) use ($user) {
                $query
                    ->whereNotIn('status', ['active', 'success', 'closed'])
                    ->orWhere(function ($q) use ($user) {
                        $q->where('status', 'active')
                            ->where(function ($subQuery) use ($user) {
                                $subQuery->where('user_id', $user->id)
                                    ->orWhereNull('user_id');
                            });
                    })
                    ->orWhere(function ($q) use ($user) {
                        $q->where('status', 'success')
                            ->where('user_id', $user->id);
                    })
                    ->orWhere(function ($q) use ($user) {
                        $q->where('status', 'closed')
                            ->where('user_id', $user->id);
                    });
            });
        }

        return $query
            ->orderByRaw("
            CASE
                WHEN status = 'new' THEN 0
                WHEN status = 'active' THEN 1
                WHEN status = 'success' THEN 2
                WHEN status = 'closed' THEN 3
                ELSE 1
            END
        ")
            ->orderBy('created_at', 'asc')
            ->paginate(16);
    }

    public function getOrdersWitchElasticSearch($request): LengthAwarePaginator
    {
        $search = $request->query('query');

        return Order::search($search)->paginate(16);
    }

    public function getOrdersWitchSearch($request): LengthAwarePaginator
    {
        $sort = strtolower($request->query('sort', 'desc'));
        $dateFrom = $request->query('dateFrom');
        $dateTo = $request->query('dateTo');
        $status = $request->query('status');
        $clientId = $request->query('client_id');
        $userId = $request->query('user_id');

        $query = Order::query()->with(['user', 'client', 'bank', 'lastMessage']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($dateFrom && $dateTo) {
            $query->whereBetween('created_at', [
                Carbon::createFromFormat('d.m.Y', $dateFrom)->startOfDay(),
                Carbon::createFromFormat('d.m.Y', $dateTo)->endOfDay()
            ]);
        } elseif ($dateFrom) {
            $query->whereDate('created_at', Carbon::createFromFormat('d.m.Y', $dateFrom));
        }

        return $query
            ->orderBy('created_at', $sort)
            ->paginate(16);
    }
    /**
     * @throws OrderNotFoundException
     */
    public function getOrder($request)
    {
        if (!$order = Order::with('messages')->find($request->getIdFromRoute('orderId'))) {
            throw new OrderNotFoundException("Заказ с ID {$request->getIdFromRoute('orderId')} не найден.");
        }
        return $order;
    }

    /**
     * @throws OrderNotFoundException
     * @throws UserNotFoundException
     */
    public function attachUserToOrder($request)
    {
        if (!$order = Order::find($request->selectedOrder['id'])) {
            throw new OrderNotFoundException("Заказ с ID {$request->selectedOrder['id']} не найден.");
        }

        if (!$user = User::find($request->selectedUser['id'])) {
            throw new UserNotFoundException("Пользователь с ID {$request->selectedUser['id']} не найден.");
        }

        $order->user()->associate($user);
        $order->save();

        return $user;
    }

    /**
     * @throws OrderNotFoundException
     */
    public function changeStatus($request): string
    {
        if (!$order = Order::find($request->getIdFromRoute('orderId'))) {
            throw new OrderNotFoundException("Заказ с ID {$request->selectedOrder['id']} не найден.");
        }

        $order->status = $request->getStatus();
        $order->save();

        return $order->status;
    }

    /**
     * @throws TelegramApiException
     * @throws OrderNotFoundException
     * @throws OrderClientNotFoundException
     */
    public function storeMessage($request)
    {
        if (!$order = Order::with('user')->find($request->getIdFromRoute('orderId'))) {
            throw new OrderNotFoundException("Заказ с ID {$request->getIdFromRoute('orderId')} не найден.");
        }

        $created_message = Message::create([
            'chat_id' => $order->chat_id,
            'order_id' => $request->getIdFromRoute('orderId'),
            'user_id' => auth()->user()->id,
            'sender_type' => 'user',
            'message' => $request->getMessage(),
        ]);

        if ($request->getIsRequisite()) {
            if ($this->redis->getRequisiteConsultant($order->chat_id)) {
                $this->redis->forgetRequisiteConsultant($order->chat_id);
                $this->sendWebhookCommand($order->chat_id, __('buttons.to_main'));
            }
            $this->clientTransferService->handleRequisiteRequest($order, $order->chat_id, $request->getMessage());
        } else {
                $this->telegramMessageService->sendMessage($order->chat_id, $request->getMessage());
        }

        return $created_message;
    }

    /**
     * @throws OrderNotFoundException
     * @throws MediaLibraryException
     */
    public function storeMessageWithPhoto($request)
    {
        if (!$order = Order::with('user')->find($request->getIdFromRoute('orderId'))) {
            throw new OrderNotFoundException("Заказ с ID {$request->getIdFromRoute('orderId')} не найден.");
        }

        $created_message = Message::create([
            'chat_id' => $order->chat_id,
            'order_id' => $request->getIdFromRoute('orderId'),
            'user_id' => auth()->user()->id,
            'sender_type' => 'user',
            'message' => null,
        ]);

        $imageContent = file_get_contents($request->file('photo')->getRealPath());
        $this->saveImageToModelFromResponse($imageContent, 'screenshot.jpg', $created_message, 'chat_screenshot');

        $created_message->refresh();

        $media = $created_message->getFirstMedia('chat_screenshot');
        $filePath = $media->getPath();

        try {
            $this->telegramMessageService->sendPhoto($order->chat_id, new \CURLFile($filePath), $request->getCaption());
        } catch (TelegramApiException|ConnectionException $e) {

        }

        return $created_message;
    }

    /**
     * @throws OrderNotFoundException
     */
    public function setMessagesRead($request): void
    {
        if (!$order = Order::find($request->getIdFromRoute('orderId'))) {
            throw new OrderNotFoundException("Заказ с ID {$request->getIdFromRoute('orderId')} не найден.");
        }
        $order->is_message = false;
        $order->save();
    }

    /**
     * @throws OrderNotFoundException
     * @throws UserNotFoundException
     * @throws ClientNotFoundException
     */
    public function closeOrder($request)
    {
        $client = Client::find($request->getClientId());

        if (! $client) {
            throw new ClientNotFoundException("Клиент с ID {$request->getClientId()} не найден.");
        }

        $client->update(['status' => __('buttons.to_main')]);

        if (!$order = Order::find($request->getOrderId())) {
            throw new OrderNotFoundException("Заказ с ID {$request->getOrderId()} не найден.");
        }

        if (!$user = User::find($request->getUserId())) {
            throw new UserNotFoundException("Пользователь с ID {$request->getUserId()} не найден.");
        }

        if ( $this->clientsService->setClientMainInput($client->bot_id, __('buttons.to_main'))) {
            // Отправка сообщение, как буд-то это было сделано клиентом в чате для возврата в главное меню
            $this->sendWebhookCommand($order->chat_id, 'start');

            $order->setRelation('user', $user);
            $order->update([
                'status' => 'closed',
                'close_at' => now()
            ]);
        }

        return $order;
    }

    /**
     * @throws UserNotFoundException
     * @throws OrderNotFoundException
     * @throws ClientNotFoundException
     */
    public function endOrder($request)
    {
        $client = Client::find($request->getClientId());

        if (! $client) {
            throw new ClientNotFoundException("Клиент с ID {$request->getClientId()} не найден.");
        }

        $client->update(['status' => __('buttons.to_main')]);

        if (!$order = Order::find($request->getOrderId())) {
            throw new OrderNotFoundException("Заказ с ID {$request->getOrderId()} не найден.");
        }

        if (!$user = User::find($request->getUserId())) {
            throw new UserNotFoundException();
        }

        if ( $this->clientsService->setClientMainInput($client->bot_id, __('buttons.to_main'))) {
            // Отправка сообщение, как буд-то это было сделано клиентом в чате для возврата в главное меню
            $this->sendWebhookCommand($order->chat_id, 'start');

            $order->setRelation('user', $user);
            $order->update([
                'end_at' => now(),
            ]);
        }

        return $order;
    }

    /**
     * @throws OrderNotFoundException
     */
    public function fixOrder($request)
    {
        $order = Order::find($request->getIdFromRoute('orderId'));

        if (!$order) {
            throw new OrderNotFoundException("Заказ с ID {$request->getIdFromRoute('orderId')} не найден.");
        }

        $order->is_pinned = !$order->is_pinned;
        $order->save();

        return $order;
    }
}
