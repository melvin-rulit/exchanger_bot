<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * The resource instance.
     *
     * @var User
     */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'login' => $this->resource->login,
            'email' => $this->resource->email,
            'last_login_at' => $this->resource->last_login_at,
            'enabled' => $this->resource->enabled,
            'role' => $this->resource->getRoleNames(),
        ];
    }
}
