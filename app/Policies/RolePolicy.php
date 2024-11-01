<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use App\Models\Role;
use App\Models\User;
use App\Traits\SuperAdminPolicyTrait;

class RolePolicy
{
    use SuperAdminPolicyTrait;

    public function index(User $user)
    {
        return $user->can(PermissionsEnum::VIEWROLES->value);
    }

    public function store(User $user)
    {
        return $user->can(PermissionsEnum::CREATEROLES->value);
    }

    public function show(User $user, Role $role)
    {
        return $role->name !== RolesEnum::SUPERADMIN->value
            && $role->name !== RolesEnum::ADMIN->value
            && $user->can(PermissionsEnum::VIEWROLES->value);
    }

    public function update(User $user, Role $role)
    {
        return $role->name !== RolesEnum::SUPERADMIN->value
            && $role->name !== RolesEnum::ADMIN->value
            && $user->can(PermissionsEnum::EDITROLES->value);
    }

    public function destroy(User $user, Role $role)
    {
        return $role->name !== RolesEnum::SUPERADMIN->value
            && $role->name !== RolesEnum::ADMIN->value
            && $user->can(PermissionsEnum::DELETEROLES->value);
    }
}
