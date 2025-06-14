<?php

namespace App\Http\Resources\Consultation;

use App\Http\Resources\User\PinedChat\PinedChatsResource;
use App\Http\Resources\User\UserResource;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Resources\BaseTypedResource;
use App\Http\Resources\Client\ClientResource;

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
            'id'           => $model->id,
            'messages'     => $model->message,
            'is_message'   => $model->is_message,
            'is_close'     => $model->is_close,
            'sender_type'  => $model->sender_type,
            'image_url'    => $model->getImageUrl(),
            'created_at'   => $model->created_at,
            'user'         => new UserResource($model->user),
            'client'       => new ClientResource($model->client),
            'chat_id'      => $model->chat_id,
            'pinned_messages' => PinedChatsResource::collection($this->whenLoaded('pinnedMessages')),
        ];
    }
}
