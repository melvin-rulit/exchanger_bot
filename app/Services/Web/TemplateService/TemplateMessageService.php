<?php

namespace App\Services\Web\TemplateService;

use App\Exceptions\Templates\TemplateCreateException;
use App\Models\TemplateMessage;
use App\Services\Web\BaseWebService;
use App\Exceptions\AuthorizationException;
use App\Exceptions\Templates\TemplatesNotFoundException;
use App\Http\Resources\Template\TemplateMessageResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TemplateMessageService extends BaseWebService
{
    /**
     * @throws TemplatesNotFoundException
     */
    public function getTemplatesMessages(): AnonymousResourceCollection
    {
        $templates = auth()->user()->templateMessages()->get();

        if ($templates->isEmpty()) {
            throw new TemplatesNotFoundException("Ни одного шаблона для user с ID " . auth()->id() . " не найдено");
        }
        return TemplateMessageResource::collection($templates);
    }

    /**
     * @throws TemplateCreateException
     */
    public function store($request): TemplateMessageResource
    {
        $newTemplate = TemplateMessage::create([
            'user_id' => auth()->id(),
            'text' => $request->getTemplate(),
        ]);

        if (!$newTemplate instanceof Model) {
            throw new TemplateCreateException('Не удалось создать шаблон.');
        }

        return new TemplateMessageResource($newTemplate);
    }

    /**
     * @throws TemplatesNotFoundException
     * @throws AuthorizationException
     */
    public function delete($request): AnonymousResourceCollection
    {
        $template = TemplateMessage::where('user_id', auth()->id())
            ->where('id', $request->getTemplateId())
            ->first();

        if (!$template) {
            throw new TemplatesNotFoundException('Шаблон не найден или доступ запрещён.');
        }

        if ($template->user_id !== auth()->id()) {
            throw new AuthorizationException('Вы не можете удалить этот шаблон.');
        }

        $template->delete();

        return $this->getTemplatesMessages();
    }
}
