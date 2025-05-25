<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Setting;

class UserObserver
{
    public function created(User $user): void
    {
        $user->assignRole('Менеджер');

        $settingIds = Setting::where('is_used', true)->pluck('id');

        if ($settingIds->isNotEmpty()) {
            $user->settings()->attach($settingIds);
        }
    }
}
