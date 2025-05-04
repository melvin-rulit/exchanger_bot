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

    public function __construct(string $message = '', ?string $dataKey = 'data', array $data = null, int $status = 200)
    {
        $response = [
            'success' => true,
            'error' => null,
            'message' => $message,
        ];

        if ($dataKey && $data !== null) {
            $response[$dataKey] = $data;
        }

        parent::__construct($response, $status);
    }
}
