<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function getUsers(): JsonResponse
    {
        $users = User::all();

//        $orders_with_screenshot = [];
//
//        foreach ($orders as $order) {
//            $media = $order->getMedia('amount_check');
//
//            if ($media->count() > 0) {
//                $media = $media->first();
//                $order->media = $media->getUrl('screenshot');
//            }
//
//            $orders_with_screenshot[] = $order;
//        }

        return new JsonResponse([
            'users' => $users
        ]);
    }
    public function getManagers(): JsonResponse
    {
        $managers = User::role('менеджер')->get();

        return new JsonResponse([
            'managers' => $managers
        ]);
    }

    public function getAuthUser(): UserResource
    {
        return new UserResource(auth()->user());
    }
}
