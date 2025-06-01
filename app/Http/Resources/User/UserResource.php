<?php

namespace App\Http\Resources\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\BaseTypedResource;
use App\Http\Resources\User\Settings\UserSettingsResource;

class UserResource extends BaseTypedResource
{
    /**
     * @extends BaseTypedResource<User>
     */
    public function toArray(Request $request): array
    {
        /** @var User $model */
        $model = $this->getResource();

        return [
            'id' => $model->id,
            'name' => $model->name,
            'email' => $model->email,
            'enabled' => $model->enabled,
            'is_locked' => $model->is_locked,
            'lock_password' => $model->lock_password,
            'role' => $model->getRoleNames(),
            'image_url'    => $model->getImageUrl(),
            'settings' => UserSettingsResource::collection($this->whenLoaded('settings')),
        ];
    }
}
