<?php

namespace App\Services\OrderService;

use App\Models\Order;
use App\Models\Client;
use App\Services\BaseService;
use App\Events\Order\NewOrder;
use App\Telegram\Traits\HandlesFile;
use App\Services\ClientService\ClientsService;
use App\Handlers\Callback\WalletCallbackHandler;
use App\Exceptions\Order\OrderNotFoundException;
use App\Services\RedisService\RedisFieldService;
use App\Services\RedisService\RedisSessionService;
use App\Services\TelegramBotService\TelegramMessageService;

class OrderService extends BaseService implements OrderServiceInterface
{
    use HandlesFile;

    public string $download_url;
    public function __construct(RedisSessionService $redis, protected ClientsService $clientsService, protected RedisFieldService $redisFieldService, protected TelegramMessageService $telegramMessageService, protected WalletCallbackHandler $walletCallbackHandler)
    {
        parent::__construct($redis);
        $this->download_url = ensure_string(config('telegram.telegram_bot.api_file_url')) . config('telegram.telegram_bot.token');
    }

    public function saveOrder($chatId, $clientId, $amount)
    {
        $client = Client::where('bot_id', $clientId)->first();

        $order = Order::create([
            'chat_id' => $chatId,
            'client_id' => $client->id,
            'user_id' => null,
            'amount' => $amount,
        ]);

        $order->refresh();
        broadcast(new NewOrder($order, 'new_order'));

        return $order;
    }

    /**
     * @throws OrderNotFoundException
     */
    public function changeStatus($orderId, $status)
    {
        if (!$order = Order::find($orderId)) {
            throw new OrderNotFoundException("Заказ с ID {$orderId} не найден.");
        }

        $order->status = $status;
        $order->is_message = false;
        $order->save();

        return $order;
    }
}
