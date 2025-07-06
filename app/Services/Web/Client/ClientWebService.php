<?php

namespace App\Services\Web\Client;


use App\Exceptions\Client\ClientNotFoundException;
use App\Exceptions\Order\OrderClientNotFoundException;
use App\Models\Client;
use App\Models\Order;
use Illuminate\Support\Collection;
use App\Services\Web\BaseWebService;
use App\Exceptions\Client\ClientsNotFoundException;


class ClientWebService extends BaseWebService
{
    /**
     * @throws ClientsNotFoundException
     */
    public function getClients(): Collection
    {
        $clients = Client::all();

        if ($clients->isEmpty()) {
            throw new ClientsNotFoundException;
        }

        return $clients;
    }


    /**
     * @throws ClientNotFoundException
     */
    public function updateClientComment($request)
    {
        $client = Client::find($request->getIdFromRoute('clientId'));

        if (!$client) {
            throw new ClientNotFoundException("Клиент не найден");
        }

        $client->update([
            'comment' => $request->getClientComment(),
        ]);

        return $request->getClientComment();
    }

    /**
     * @throws OrderClientNotFoundException
     */
    public function updateClientName($request): void
    {
        $order = Order::with('client')->find($request->getIdFromRoute('orderId'));

        if (!$order->client) {
            throw new OrderClientNotFoundException("Заказу с ID {$request->getIdFromRoute('orderId')} не присвоен клиент");
        }

        $order->client->update([
            'first_name' => $request->getClientName(),
        ]);
    }
}
