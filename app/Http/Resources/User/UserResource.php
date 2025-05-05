<?php

namespace App\Http\Resources\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\BaseTypedResource;

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
            'login' => $model->login,
            'email' => $model->email,
            'last_login_at' => $model->last_login_at,
            'enabled' => $model->enabled,
            'role' => $model->getRoleNames(),
        ];
    }
}
