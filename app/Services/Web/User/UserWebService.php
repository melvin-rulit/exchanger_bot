<?php

namespace App\Services\Web\User;

use App\Exceptions\User\Chat\PinnedChatsNotFoundException;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserPinnedMessage;
use Illuminate\Support\Collection;
use App\Services\Web\BaseWebService;
use App\Exceptions\User\UsersNotFoundException;
use App\Exceptions\User\AuthUserNotFoundException;
use App\Exceptions\User\ManagersNotFoundException;
use App\Exceptions\User\Chat\PinChatFailedException;
use App\Exceptions\User\Chat\PinnedChatNotFoundException;
use App\Exceptions\LockScreen\LockPasswordMismatchException;

class UserWebService extends BaseWebService
{
    /**
     * @throws UsersNotFoundException
     */
    public function getUsers(): Collection
    {
        $users = User::all();

        if ($users->isEmpty()) {
            throw new UsersNotFoundException;
        }

        return $users;
    }

    /**
     * @throws ManagersNotFoundException
     */
    public function getManagers(): Collection
    {
        $managers = User::role('менеджер')->get();

        if ($managers->isEmpty()) {
            throw new ManagersNotFoundException;
        }

        return $managers;
    }

    /**
     * @throws AuthUserNotFoundException
     */
    public function getAuthUser(): User
    {
        $user = auth()->user();

        if (!$user) {
            throw new AuthUserNotFoundException();
        }

        return $user;
    }

    /**
     * @throws AuthUserNotFoundException
     */
    public function lockScreen(): void
    {
        $user = auth()->user();

        if (!$user instanceof User) {
            throw new AuthUserNotFoundException();
        }

        $user->update([
            'is_locked' => true
        ]);
    }

    /**
     * @throws AuthUserNotFoundException
     */
    public function setPasswordForLock($request): void
    {
        $user = auth()->user();

        if (!$user instanceof User) {
            throw new AuthUserNotFoundException();
        }

        $user->update([
            'lock_password' => $request->getPassword()
        ]);
    }

    /**
     * @throws AuthUserNotFoundException
     */
    public function sendPasswordForUnlock($request)
    {
        $user = auth()->user();

        if (!$user instanceof User) {
            throw new AuthUserNotFoundException();
        }

        if ($request->getPassword() !== $user->lock_password) {
            return new LockPasswordMismatchException;
        }

        $user->update([
            'is_locked' => false
        ]);
    }

    /**
     * @throws PinnedChatsNotFoundException
     */
    public function getPinnedChat(): Collection
    {
        $pinnedChat = UserPinnedMessage::whereDate('created_at', Carbon::today())->get();

        if ($pinnedChat->isEmpty()) {
            throw new PinnedChatsNotFoundException;
        }

        return $pinnedChat;
    }

    /**
     * @throws PinChatFailedException
     * @throws AuthUserNotFoundException
     */
    public function pinChat($request)
    {
        $user = auth()->user();

        if (!$user instanceof User) {
            throw new AuthUserNotFoundException();
        }

        $pinnedChat = UserPinnedMessage::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'client_id' => $request->getClientId(),
                'order_id' => $request->getOrderId(),
            ],
            [
                'is_active' => true,
                'is_pinned' => true,
            ]
        );

        if (!$pinnedChat) {
            throw new PinChatFailedException();
        }

        return $pinnedChat;
    }

    /**
     * @throws PinnedChatNotFoundException
     * @throws PinChatFailedException
     */
    public function unPinChat($request)
    {
        $pinnedChat = UserPinnedMessage::find($request->getChatId());

        if (!$pinnedChat) {
            throw new PinnedChatNotFoundException();
        }

        $deletedChat = $pinnedChat->delete();

        if (!$deletedChat) {
            throw new PinChatFailedException();
        }

        return $pinnedChat;
    }
}
