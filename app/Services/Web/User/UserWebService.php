<?php

namespace App\Services\Web\User;


use App\Exceptions\Images\MediaLibraryException;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserPinnedMessage;
use Illuminate\Support\Collection;
use App\Services\Web\BaseWebService;
use App\Telegram\Traits\HandlesFile;
use App\Exceptions\User\UserNotFoundException;
use App\Exceptions\User\UsersNotFoundException;
use App\Exceptions\User\AuthUserNotFoundException;
use App\Exceptions\User\ManagersNotFoundException;
use App\Exceptions\User\Chat\PinChatFailedException;
use App\Exceptions\User\Chat\PinnedChatNotFoundException;
use App\Exceptions\LockScreen\LockPasswordMismatchException;

class UserWebService extends BaseWebService
{
    use HandlesFile;

    /**
     * @throws UsersNotFoundException
     */
    public function getUsers(): Collection
    {
        $users = User::with('settings')->get();

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

        if (!$managers) {
            throw new ManagersNotFoundException;
        }

        return $managers;
    }

    /**
     * @throws AuthUserNotFoundException
     */
    public function getAuthUser()
    {
        $user = User::with('settings')->find(auth()->id());

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

    public function getPinnedChat(): Collection
    {
        return UserPinnedMessage::whereDate('created_at', Carbon::today())
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'asc')
            ->get();
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
                'order_id' => $request->getOrderId()?: null,
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
        $chatId  = $request->getChatId();
        $orderId = $request->getOrderId();

        if ($chatId !== null) {
            $pinnedChat = UserPinnedMessage::find($chatId);
        } elseif ($orderId !== null) {
            $pinnedChat = UserPinnedMessage::where('order_id', $orderId)->first();
        } else {
            throw new PinChatFailedException('Нельзя определить, что откреплять: нет ни chatId, ни orderId');
        }

        if (!$pinnedChat) {
            throw new PinnedChatNotFoundException();
        }

        if (!$pinnedChat->delete()) {
            throw new PinChatFailedException();
        }

        return $pinnedChat;
    }

    public function toggleNotification($request)
    {
        $user = auth()->user();

        $user->settings()->updateExistingPivot($request->getSettingId(), [
            'is_active' => !$request->getSettingIsActive(),
        ]);

        return !$request->getSettingIsActive();
    }

    /**
     * @throws UserNotFoundException
     */
    public function update($request)
    {
        $user = User::find($request->getIdFromRoute('userId'));

        if (!$user) {
            throw new UserNotFoundException();
        }

        $user->update([
            'name' => $request->getName(),
        ]);

        return $user;
    }

    /**
     * @throws MediaLibraryException
     * @throws AuthUserNotFoundException
     */
    public function storeUserPhoto($request): void
    {
        $user = auth()->user();

        if (!$user instanceof User) {
            throw new AuthUserNotFoundException();
        }

        $imageContent = file_get_contents($request->file('photo')->getRealPath());
        $this->saveImageToModelFromResponse($imageContent, 'screenshot.jpg', $user, 'user_avatar');
    }
}
