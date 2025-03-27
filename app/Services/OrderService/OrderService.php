<?php

namespace App\Services\OrderService;

use App\Events\UserMessageSent;
use App\Models\Client;
use App\Models\Currency;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class OrderService implements OrderServiceInterface
{
    public function saveOrder($chatId, $clientId, $amount, $currency_id)
    {
        $client = Client::where('bot_id', $clientId)->first();
        $currency = Currency::find($currency_id);

        $order = Order::create([
            'chat_id' => $chatId,
            'client_id' => $client->id,
            'user_id' => null,
            'amount' => $amount,
            'currency_name' => $currency->name,
        ]);

        broadcast(new UserMessageSent($order));
        return $order;
    }
    public function getOrdersForClient($clientId)
    {

    }

    public function setOrderStatus($orderId, $status)
    {

    }
}
