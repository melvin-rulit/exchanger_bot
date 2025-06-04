<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Illuminate\Http\Request;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        if ($user) {

            $user->load('settings');

            $plainSettings = $user->settings->map(function ($setting) {
                $arr = $setting->toArray();
                $arr['is_active'] = $setting->pivot->is_active;
                $arr['value']     = $setting->pivot->value;
                unset($arr['pivot']);
                return $arr;
            })->toArray();

            $userArray = $user->toArray();

            $userArray['settings'] = $plainSettings;
            $userArray['image_url'] = $user->getImageUrl();
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $userArray ?? null,
                'role' => $user?->getRoleNames()->first(),
            ],
        ];
    }
}
