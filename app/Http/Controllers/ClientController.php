<?php

namespace App\Http\Controllers;

use App\Exceptions\Client\ClientsNotFoundException;
use App\Http\Responses\NotFoundResponse;
use App\Http\Resources\Client\ClientResource;
use App\Services\Web\Client\ClientWebService;
use App\Exceptions\User\UsersNotFoundException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientController extends Controller
{
    public function __construct(protected ClientWebService $clientWebService){}

    /**
     * @throws ClientsNotFoundException
     */
    public function getClients(): NotFoundResponse|AnonymousResourceCollection
    {
        $clients = $this->clientWebService->getClients();
        return ClientResource::collection($clients);
    }
}
