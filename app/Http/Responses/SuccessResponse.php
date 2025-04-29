<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

final class SuccessResponse extends JsonResponse
{
    /**
     * @var string
     *
     */
    protected string $message;

    public function __construct(string $message = '', string $dataKey = 'data', array $data = null, int $status = 200)
    {
        parent::__construct([
                'success' => true,
                'error' => null,
                'message' => $message,
                $dataKey => $data,
            ], $status);
    }
}
