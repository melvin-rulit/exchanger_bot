<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderMessagesResource extends JsonResource
{
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->resource->id,
            'message' => $this->resource->message,
            'sender_type' => $this->resource->sender_type,
            'created_at' => $this->resource->created_at
        ];

        return $data;
    }
}
