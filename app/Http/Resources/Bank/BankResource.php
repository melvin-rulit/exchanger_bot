<?php

namespace App\Http\Resources\Bank;

use App\Models\Bank;
use Illuminate\Http\Request;
use App\Http\Resources\BaseTypedResource;

class BankResource extends BaseTypedResource
{
    /**
     * @extends BaseTypedResource<Bank>
     */
    public function toArray(Request $request): array
    {
        /** @var Bank $model */
        $model = $this->getResource();

        return [
            'id' => $model->id,
            'name' => $model->name,
        ];
    }
}
