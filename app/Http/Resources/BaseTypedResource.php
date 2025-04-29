<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @template T of Model
 */
abstract class BaseTypedResource extends JsonResource
{
    /**
     * @phpstan-return T
     * @psalm-return T
     */
    public function getResource(): object
    {
        return $this->resource;
    }
}
