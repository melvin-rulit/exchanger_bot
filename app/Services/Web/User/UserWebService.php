<?php

namespace App\Services\Web\User;


use Carbon\Carbon;
use App\Models\User;
use App\Models\UserPinnedMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Services\Web\BaseWebService;
use App\Telegram\Traits\HandlesFile;
use App\Exceptions\User\UserNotFoundException;
use App\Exceptions\User\UsersNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\Images\MediaLibraryException;
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
    public function getUsers(): LengthAwarePaginator
    {
        $users = User::with('settings')
            ->withCount(['orders as active_orders_count' => function ($query) {
                $query->where('status', 'active');
            }])
            ->withCount(['orders as success_orders_count' => function ($query) {
                $query->where('status', 'success');
            }])
            ->withCount(['orders as closed_orders_count' => function ($query) {
                $query->where('status', 'closed');
            }])
            ->paginate(16);

        if ($users->isEmpty()) {
            throw new UsersNotFoundException;
        }

        return $users;
    }

    public function getUsersWitchSearch($request)
    {
        $dateFrom = $request->query('dateFrom');
        $dateTo = $request->query('dateTo');

        return User::whereHas('orders', function ($query) use ($dateFrom, $dateTo) {
                if ($dateFrom && $dateTo) {
                    $query->whereBetween('created_at', [
                        Carbon::createFromFormat('d.m.Y', $dateFrom)->startOfDay(),
                        Carbon::createFromFormat('d.m.Y', $dateTo)->endOfDay()
                    ]);
                } elseif ($dateFrom) {
                    $query->whereDate('created_at', Carbon::createFromFormat('d.m.Y', $dateFrom));
                }
            })
                ->with('settings')
                ->withCount([
                    'orders as active_orders_count' => function ($query) use ($dateFrom, $dateTo) {
                        $query->where('status', 'active');
                        if ($dateFrom && $dateTo) {
                            $query->whereBetween('created_at', [
                                Carbon::createFromFormat('d.m.Y', $dateFrom)->startOfDay(),
                                Carbon::createFromFormat('d.m.Y', $dateTo)->endOfDay()
                            ]);
                        } elseif ($dateFrom) {
                            $query->whereDate('created_at', Carbon::createFromFormat('d.m.Y', $dateFrom));
                        }
                    },
                    'orders as success_orders_count' => function ($query) use ($dateFrom, $dateTo) {
                        $query->where('status', 'success');
                        if ($dateFrom && $dateTo) {
                            $query->whereBetween('created_at', [
                                Carbon::createFromFormat('d.m.Y', $dateFrom)->startOfDay(),
                                Carbon::createFromFormat('d.m.Y', $dateTo)->endOfDay()
                            ]);
                        } elseif ($dateFrom) {
                            $query->whereDate('created_at', Carbon::createFromFormat('d.m.Y', $dateFrom));
                        }
                    },
                    'orders as closed_orders_count' => function ($query) use ($dateFrom, $dateTo) {
                        $query->where('status', 'closed');
                        if ($dateFrom && $dateTo) {
                            $query->whereBetween('created_at', [
                                Carbon::createFromFormat('d.m.Y', $dateFrom)->startOfDay(),
                                Carbon::createFromFormat('d.m.Y', $dateTo)->endOfDay()
                            ]);
                        } elseif ($dateFrom) {
                            $query->whereDate('created_at', Carbon::createFromFormat('d.m.Y', $dateFrom));
                        }
                    }
                ])
                ->paginate(16);
    }


    /**
     * @throws ManagersNotFoundException
     */
    public function getManagers(): LengthAwarePaginator
    {
        $managers = User::role('менеджер')->paginate(16);

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

    public function toggleNotification($request): bool
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
    public function updateUser($request)
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
     * @throws UserNotFoundException
     */
    public function updateFieldsUser($request)
    {
        $user = User::find($request->getIdFromRoute('userId'));

        if (!$user) {
            throw new UserNotFoundException();
        }

        $user->update($request->only(['name', 'email', 'password_show']));

        if ($request->filled('password_show')) {
            $user->password = bcrypt($request->input('password_show'));
            $user->save();
        }

        return $user;
    }

    /**
     * @throws UserNotFoundException
     */
    public function deleteUser($request): void
    {
        $user = User::find($request->getIdFromRoute('userId'));

        if (!$user) {
            throw new UserNotFoundException();
        }

        DB::table('sessions')->where('user_id', $user->id)->delete();

        $user->delete();
    }

    /**
     * @throws UserNotFoundException
     */
    public function updateRole($request)
    {
        $user = User::find($request->getIdFromRoute('userId'));

        if (!$user) {
            throw new UserNotFoundException();
        }

        $role = Role::find($request->getRoleId());

        if ($role) {
            $user->syncRoles($role);
        }

        return $user;
    }

    /**
     * @throws UserNotFoundException
     */
    public function updateStatus($request)
    {
        $user = User::find($request->getIdFromRoute('userId'));

        if (!$user) {
            throw new UserNotFoundException();
        }

        $user->update([
            'enabled' => $request->getStatusId()
        ]);

        return $user;
    }

    /**
     * @throws MediaLibraryException
     * @throws AuthUserNotFoundException
     */
    public function storeUserPhoto($request): User
    {
        $user = auth()->user();

        if (!$user instanceof User) {
            throw new AuthUserNotFoundException();
        }

        $imageContent = file_get_contents($request->file('photo')->getRealPath());
        $this->saveImageToModelFromResponse($imageContent, 'screenshot.jpg', $user, 'user_avatar');

        $user->refresh();
        return $user;
    }
}
