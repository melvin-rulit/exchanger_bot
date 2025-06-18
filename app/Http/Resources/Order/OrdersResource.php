<?php

namespace App\Http\Resources\Order;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Resources\Bank\BankResource;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\BaseTypedResource;
use App\Http\Resources\Client\ClientResource;
use App\Http\Resources\User\PinedChat\PinedChatsResource;


class OrdersResource extends BaseTypedResource
{
    /**
     * @extends BaseTypedResource<Order>
     */
    public function toArray(Request $request): array
    {
        /** @var Order $model */
        $model = $this->getResource();

        return [
            'id' => $model->id,
            'client'  => ClientResource::make($model->client),
            'user' => UserResource::make($model->user),
            'bank' => BankResource::make($model->bank),
            'amount' => $model->amount,
            'status' => $model->status,
            'is_message' => $model->is_message,
            'is_pinned' => $model->is_pinned,
            'is_requisite' => $model->is_requisite,
            'image_url'    => $model->getImageUrl(),
            'created_at' => $model->created_at,
            'close_at' => $model->close_at,
            'pinned_messages' => PinedChatsResource::collection($this->whenLoaded('pinnedMessages')),
            ];
    }
}
