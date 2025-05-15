<?php

namespace App\Http\Resources\Client;


use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Resources\BaseTypedResource;

class ClientResource extends BaseTypedResource
{
    /**
     * @extends BaseTypedResource<Client>
     */
    public function toArray(Request $request): array
    {
        /** @var Client $model */
        $model = $this->getResource();

        return [
            'id' => $model->id,
            'first_name' => $model->first_name,
            'bot_id' => $model->bot_id,
            'status' => $model->status,
        ];
    }
}
