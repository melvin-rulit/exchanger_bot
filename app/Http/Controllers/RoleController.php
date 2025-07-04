<?php

namespace App\Http\Controllers;

use App\Services\Web\Role\RoleService;
use App\Http\Responses\NotFoundResponse;
use App\Http\Resources\Role\RoleResource;
use App\Exceptions\Role\RoleNotFoundException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoleController extends Controller
{
    public function __construct(protected RoleService $roleService){}
    public function getRoles(): AnonymousResourceCollection|NotFoundResponse
    {
        try {
            $roles = $this->roleService->getRoles();
            return RoleResource::collection($roles);

        } catch (RoleNotFoundException $e) {
            return new NotFoundResponse($e->getMessage());
        }
    }
}
