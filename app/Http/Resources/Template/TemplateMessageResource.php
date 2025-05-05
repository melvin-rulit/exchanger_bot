<?php

namespace App\Http\Resources\Template;

use Illuminate\Http\Request;
use App\Models\TemplateMessage;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\BaseTypedResource;


class TemplateMessageResource extends BaseTypedResource
{
    /**
     * @extends BaseTypedResource<TemplateMessage>
     */
    public function toArray(Request $request): array
    {
        /** @var TemplateMessage $model */
        $model = $this->getResource();

        return [
            'id' => $model->id,
//            'user' => UserResource::make($model->user),
            'title' => $model->title,
            'text' => $model->text,
            'category' => $model->category,
            'created_at' => $model->created_at,
            ];
    }
}
