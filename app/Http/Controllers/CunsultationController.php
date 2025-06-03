<?php

namespace App\Http\Controllers;

use App\Http\Responses\ErrorResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\NotFoundResponse;
use App\Exceptions\TelegramApiException;
use App\Exceptions\Order\OrderNotFoundException;
use App\Exceptions\Images\MediaLibraryException;
use App\Http\Resources\Consultation\MessageResource;
use App\Http\Resources\Consultation\ChatMessageResource;
use App\Services\Web\Consultation\ConsultationWebService;
use App\Exceptions\Consultation\MessageNotFoundException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\Consultation\CreateConsultantMessageRequest;
use App\Http\Requests\Consultation\SetConsultantMessageReadRequest;
use App\Http\Requests\Consultation\CreateConsultantMessageWithPhotoRequest;

class CunsultationController extends Controller
{
    public function __construct(protected ConsultationWebService $consultationWebService){}
    public function getMessages(): AnonymousResourceCollection
    {
            $messages = $this->consultationWebService->getTodayConsultationMessages();
            return MessageResource::collection($messages);
    }

    public function getTodayMessages(int $message_id): NotFoundResponse|AnonymousResourceCollection
    {
        try {
            $messages = $this->consultationWebService->getTodayMessagesChat($message_id);
            return ChatMessageResource::collection($messages);

        } catch (MessageNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }

    /**
     * @throws TelegramApiException
     */
    public function storeMessage(CreateConsultantMessageRequest $request): ChatMessageResource|NotFoundResponse
    {
        try {
            $message = $this->consultationWebService->storeMessage($request);
            return new ChatMessageResource($message);

        } catch (MessageNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }
    public function storeMessageWithPhoto(CreateConsultantMessageWithPhotoRequest $request): NotFoundResponse|ErrorResponse|ChatMessageResource
    {
        try {
            $message = $this->consultationWebService->storeMessageWithPhoto($request);
            return new ChatMessageResource($message);

        } catch (MediaLibraryException $e) {
            return new ErrorResponse('Не удалось сохранить изображение: ' . $e->getMessage());
        } catch (OrderNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }
    public function setMessagesConsultantRead(SetConsultantMessageReadRequest $request): SuccessResponse|NotFoundResponse
    {
        try {
            $this->consultationWebService->setMessagesRead($request);
            return new SuccessResponse('Сообщение отмечено прочитанным');

        } catch (MessageNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }
}
