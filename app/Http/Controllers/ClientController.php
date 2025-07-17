<?php

namespace App\Http\Controllers;

use App\Exceptions\Client\ClientNotFoundException;
use App\Exceptions\Client\ClientsNotFoundException;
use App\Exceptions\Order\OrderClientNotFoundException;
use App\Http\Requests\Order\UpdateClientCommentRequest;
use App\Http\Requests\Order\UpdateClientNameRequest;
use App\Http\Responses\NotFoundResponse;
use App\Http\Resources\Client\ClientResource;
use App\Http\Responses\SuccessResponse;
use App\Services\Web\Client\ClientWebService;
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

    /**
     * @throws ClientNotFoundException
     */
    public function updateClientComment(UpdateClientCommentRequest $request): SuccessResponse
    {
        $comment = $this->clientWebService->updateClientComment($request);
        return new SuccessResponse('Комментарий успешно записан.', 'comment', ['comment' => $comment]);
    }

    public function updateClientName(UpdateClientNameRequest $request): NotFoundResponse|SuccessResponse
    {
        try {
            $this->clientWebService->updateClientName($request);
            return new SuccessResponse('Имя клиента успешно обновлено.');

        } catch (OrderClientNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }
}
