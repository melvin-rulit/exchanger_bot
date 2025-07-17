<?php

namespace App\Http\Controllers;

use App\Http\Responses\SuccessResponse;
use App\Exceptions\AuthorizationException;
use App\Exceptions\Templates\TemplateCreateException;
use App\Exceptions\Templates\TemplatesNotFoundException;
use App\Http\Requests\Template\NewTemplateMessageRequest;
use App\Services\Web\TemplateService\TemplateMessageService;
use App\Http\Requests\Template\DeleteTemplateMessageRequest;
use App\Http\Requests\Template\UpdateTemplateMessageRequest;

class TemplateMessageController extends Controller
{
    public function __construct(protected TemplateMessageService $templateMessageService){}

    public function getTemplates(): SuccessResponse
    {
        $messages = $this->templateMessageService->getTemplatesMessages();
        return new SuccessResponse('Найденные шаблоны сообщений', 'template', ['messages' => $messages]);
    }

    /**
     * @throws TemplateCreateException
     */
    public function storeTemplate(NewTemplateMessageRequest $request): SuccessResponse
    {
        $messages = $this->templateMessageService->store($request);
        return new SuccessResponse('Новый шаблон сохранен', 'template', ['messages' => $messages]);
    }

    /**
     * @throws TemplatesNotFoundException
     */
    public function updateTemplate(UpdateTemplateMessageRequest $request): SuccessResponse
    {
        $this->templateMessageService->update($request);
        return new SuccessResponse('Шаблон обновлен');
    }

    /**
     * @throws TemplatesNotFoundException
     * @throws AuthorizationException
     */
    public function deleteTemplate(DeleteTemplateMessageRequest $request): SuccessResponse
    {
        $messages = $this->templateMessageService->delete($request);
        return new SuccessResponse('Шаблон удален', 'template', ['messages' => $messages]);
    }
}
