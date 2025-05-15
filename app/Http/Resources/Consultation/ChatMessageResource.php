<?php

namespace App\Http\Resources\Consultation;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\BaseTypedResource;
use App\Http\Resources\Client\ClientResource;

class ChatMessageResource extends BaseTypedResource
{
    /**
     * @extends BaseTypedResource<Message>
     */
    public function toArray(Request $request): array
    {
        /** @var Message $model */
        $model = $this->getResource();

        return [
            'id'           => $model->id,
            'message'      => $model->message,
            'is_message'   => $model->is_message,
            'is_close'     => $model->is_close,
            'user'         => new UserResource($model->user),
            'sender_type'  => $model->sender_type,
            'image_url'    => $model->getImageUrl(),
            'created_at'   => $model->created_at,
            'client'       => new ClientResource($model->client),
        ];
    }
}
