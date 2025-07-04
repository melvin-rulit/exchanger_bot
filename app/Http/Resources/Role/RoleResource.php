<?php

namespace App\Http\Resources\Role;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Resources\BaseTypedResource;


class RoleResource extends BaseTypedResource
{
    /**
     * @extends BaseTypedResource<Role>
     */
    public function toArray(Request $request): array
    {
        /** @var Role $model */
        $model = $this->getResource();

        return [
            'id' => $model->id,
            'name' => $model->name,
            ];
    }
}
