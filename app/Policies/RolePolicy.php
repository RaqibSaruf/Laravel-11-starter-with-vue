<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use App\Models\Role;
use App\Models\User;

class RolePolicy extends Policy
{
    public function index(User $user)
    {
        return $user->can(PermissionsEnum::VIEW_ROLES->value);
    }

    public function store(User $user)
    {
        return $user->can(PermissionsEnum::CREATE_ROLES->value);
    }

    public function show(User $user, Role $role)
    {
        return $role->name !== RolesEnum::SUPERADMIN->value
            && $role->name !== RolesEnum::ADMIN->value
            && $user->can(PermissionsEnum::VIEW_ROLES->value);
    }

    public function update(User $user, Role $role)
    {
        return $role->name !== RolesEnum::SUPERADMIN->value
            && $role->name !== RolesEnum::ADMIN->value
            && $user->can(PermissionsEnum::EDIT_ROLES->value);
    }

    public function destroy(User $user, Role $role)
    {
        return $role->name !== RolesEnum::SUPERADMIN->value
            && $role->name !== RolesEnum::ADMIN->value
            && $user->can(PermissionsEnum::DELETE_ROLES->value);
    }
}
