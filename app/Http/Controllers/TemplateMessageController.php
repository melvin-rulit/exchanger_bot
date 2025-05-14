<?php

namespace App\Http\Controllers;

use App\Http\Responses\ErrorResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\NotFoundResponse;
use App\Exceptions\AuthorizationException;
use App\Http\Responses\ErrorCreateResponse;
use App\Exceptions\Templates\TemplateCreateException;
use App\Exceptions\Templates\TemplatesNotFoundException;
use App\Http\Requests\Template\NewTemplateMessageRequest;
use App\Services\Web\TemplateService\TemplateMessageService;
use App\Http\Requests\Template\DeleteTemplateMessageRequest;
use App\Http\Requests\Template\UpdateTemplateMessageRequest;

class TemplateMessageController extends Controller
{
    public function __construct(protected TemplateMessageService $templateMessageService){}

    public function getTemplates(): SuccessResponse|NotFoundResponse
    {
        try {
            $messages = $this->templateMessageService->getTemplatesMessages();
            return new SuccessResponse('Найденные шаблоны сообщений', 'template', ['messages' => $messages]);

        } catch (TemplatesNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }
    public function storeTemplate(NewTemplateMessageRequest $request): SuccessResponse|ErrorCreateResponse
    {
        try {
            $messages = $this->templateMessageService->store($request);
            return new SuccessResponse('Новый шаблон сохранен', 'template', ['messages' => $messages]);

        } catch (TemplateCreateException $e) {
            return new ErrorCreateResponse($e->getMessage());
        }
    }
    public function updateTemplate(UpdateTemplateMessageRequest $request): SuccessResponse|NotFoundResponse
    {
        try {
            $this->templateMessageService->update($request);
            return new SuccessResponse('Шаблон обновлен');

        } catch (TemplatesNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }

    public function deleteTemplate(DeleteTemplateMessageRequest $request): SuccessResponse|NotFoundResponse|ErrorResponse
    {
        try {
            $messages = $this->templateMessageService->delete($request);
            return new SuccessResponse('Шаблон удален', 'template', ['messages' => $messages]);

        } catch (TemplatesNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        } catch (AuthorizationException $e) {
            return new ErrorResponse('Недостаточно прав для удаления шаблона', 403);
        }
    }
}
