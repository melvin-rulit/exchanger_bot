<?php

namespace App\Http\Resources\User\PinedChat;

use Illuminate\Http\Request;
use App\Models\UserPinnedMessage;
use App\Http\Resources\BaseTypedResource;
use App\Http\Resources\Order\OrdersResource;

class PinedChatsResource extends BaseTypedResource
{
    protected bool $forceIsPinned = true;

    public function withForcedPinned(bool $value): self
    {
        $this->forceIsPinned = $value;
        return $this;
    }
    /**
     * @extends BaseTypedResource<UserPinnedMessage>
     */
    public function toArray(Request $request): array
    {
        /** @var UserPinnedMessage $model */
        $model = $this->getResource();

        return [
            'id' => $model->id,
            'order' => OrdersResource::make($model->order),
            'is_active' => $model->is_active,
            'is_pinned' => $this->forceIsPinned ? $model->is_pinned : false,
        ];
    }
}

