<?php

namespace App\Http\Resources\Order;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Resources\BaseTypedResource;

class MessageResource extends BaseTypedResource
{
    /**
     * @extends BaseTypedResource<Message>
     */
    public function toArray(Request $request): array
    {
        /** @var Message $model */
        $model = $this->getResource();

        return [
            'message' => $model->message,
        ];
    }
}
