<?php

namespace App\Services\Web\Role;

use Spatie\Permission\Models\Role;
use App\Services\Web\BaseWebService;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\Role\RoleNotFoundException;

class RoleService extends BaseWebService
{
    /**
     * @throws RoleNotFoundException
     */
    public function getRoles(): Collection
    {
        $roles = Role::all();

        if ($roles->isEmpty()) {
            throw new RoleNotFoundException();
        }
        return $roles;
    }
}
