<?php

namespace App\Http\Responses;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\User\UserResource;

final class AuthSuccessResponse extends JsonResponse
{
    /**
     * @var string
     *
     */
    protected string $message;

    public function __construct(string $token, User $user, int $status = 200)
    {
        parent::__construct([
                'success' => true,
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in_minutes' => config('sanctum.expiration'),
                'user' => new UserResource($user),
            ], $status);
    }
}
