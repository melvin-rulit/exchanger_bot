<?php

namespace App\Services\Web\Client;


use App\Models\Client;
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
}
