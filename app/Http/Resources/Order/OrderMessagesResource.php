<?php

namespace App\Http\Resources\Order;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Resources\BaseTypedResource;

class OrderMessagesResource extends BaseTypedResource
{
    /**
     * @extends BaseTypedResource<Message>
     */
    public function toArray(Request $request): array
    {
        /** @var Message $model */
        $model = $this->getResource();

        return [
            'id' => $model->id,
            'message' => $model->message,
            'sender_type' => $model->sender_type,
            'created_at' => $model->created_at,
            ];
    }
}
