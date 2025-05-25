<?php

namespace App\Http\Resources\User\Settings;

use App\Models\UserSetting;
use Illuminate\Http\Request;
use App\Http\Resources\BaseTypedResource;

class UserSettingsResource extends BaseTypedResource
{
    /**
     * @extends BaseTypedResource<UserSetting>
     */
    public function toArray(Request $request): array
    {
        /** @var UserSetting $model */
        $model = $this->getResource();

        return [
            'id' => $model->id,
            'key' => $model->key,
            'is_used' => $model->is_used,
            'is_active' => (bool) $this->pivot->is_active,
        ];
    }
}
