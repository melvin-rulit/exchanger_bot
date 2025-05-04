<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

final class ErrorCreateResponse extends JsonResponse
{
    /**
     * @var string
     *
     */
    protected string $message;

    public function __construct(string $message, int $status = 500)
    {
        parent::__construct([
                'success' => false,
                'error' => $message,
            ], $status);
    }
}
