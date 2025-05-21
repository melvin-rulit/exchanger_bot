<?php

namespace App\Http\Controllers;

use App\Http\Responses\ErrorResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\NotFoundResponse;
use App\Http\Resources\User\UserResource;
use App\Services\Web\User\UserWebService;
use App\Exceptions\User\UsersNotFoundException;
use App\Http\Requests\User\UnlockScreenRequest;
use App\Exceptions\User\AuthUserNotFoundException;
use App\Exceptions\User\ManagersNotFoundException;
use App\Http\Requests\User\PinedChat\PinChatRequest;
use App\Exceptions\User\Chat\PinChatFailedException;
use App\Http\Requests\User\PinedChat\UnPinChatRequest;
use App\Http\Requests\User\SetLockScreenPasswordRequest;
use App\Http\Resources\User\PinedChat\PinedChatsResource;
use App\Exceptions\User\Chat\PinnedChatNotFoundException;
use App\Exceptions\User\Chat\PinnedChatsNotFoundException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(protected UserWebService $userWebService){}

    public function getUsers(): NotFoundResponse|AnonymousResourceCollection
    {
        try {
            $users = $this->userWebService->getUsers();
            return UserResource::collection($users);

        } catch (UsersNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }

    }
    public function getManagers(): NotFoundResponse|AnonymousResourceCollection
    {
        try {
            $managers = $this->userWebService->getManagers();
            return UserResource::collection($managers);

        } catch (ManagersNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }

    public function getAuthUser(): NotFoundResponse|AnonymousResourceCollection|UserResource
    {
        try {
            $authUser = $this->userWebService->getAuthUser();
            return new UserResource($authUser);

        } catch (AuthUserNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }

    public function lockScreen(): SuccessResponse|NotFoundResponse
    {
        try {
            $this->userWebService->lockScreen();
            return new SuccessResponse('Экран заблокирован');

        } catch (AuthUserNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }
    public function sendPasswordForUnlock(UnlockScreenRequest $request): NotFoundResponse|SuccessResponse
    {
        try {
            $this->userWebService->sendPasswordForUnlock($request);
            return new SuccessResponse('Экран разблокирован');

        } catch (AuthUserNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }
    public function setPasswordForLock(SetLockScreenPasswordRequest $request): NotFoundResponse|SuccessResponse
    {
        try {
            $this->userWebService->setPasswordForLock($request);
            return new SuccessResponse('Пароль установлен');

        } catch (AuthUserNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }
    public function getPinnedChat(): AnonymousResourceCollection|NotFoundResponse
    {
        try {
            $pinnedChat = $this->userWebService->getPinnedChat();
            return PinedChatsResource::collection($pinnedChat);

        } catch (PinnedChatsNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }
    public function pinChat(PinChatRequest $request): PinedChatsResource|ErrorResponse|NotFoundResponse
    {
        try {
            $pinnedChat = $this->userWebService->pinChat($request);
            return new PinedChatsResource($pinnedChat);

        } catch (PinChatFailedException $e) {
            return new ErrorResponse($e->getMessage());
        } catch (AuthUserNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }

    public function unPinChat(UnPinChatRequest $request): ErrorResponse|NotFoundResponse|PinedChatsResource
    {
        try {
            $pinnedChat = $this->userWebService->unPinChat($request);
            return (new PinedChatsResource($pinnedChat))->withForcedPinned(false);

        } catch (PinChatFailedException $e) {
            return new ErrorResponse($e->getMessage());
        } catch (PinnedChatNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }
}
