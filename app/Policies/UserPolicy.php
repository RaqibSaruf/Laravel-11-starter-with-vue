<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use App\Models\User;

class UserPolicy extends Policy
{
    public function index(User $authUser)
    {
        return $authUser->can(PermissionsEnum::VIEW_USERS->value);
    }

    public function store(User $authUser)
    {
        return $authUser->can(PermissionsEnum::CREATE_USERS->value);
    }

    public function show(User $authUser, User $user)
    {
        if ($authUser->id !== $user->id && !$user->hasRole([RolesEnum::SUPERADMIN->value, RolesEnum::ADMIN->value])) {
            return false;
        }

        return $authUser->can(PermissionsEnum::VIEW_USERS->value);
    }

    public function update(User $authUser, User $user)
    {
        if ($authUser->id !== $user->id && !$user->hasRole([RolesEnum::SUPERADMIN->value, RolesEnum::ADMIN->value])) {
            return false;
        }

        return $authUser->can(PermissionsEnum::EDIT_USERS->value);
    }

    public function destroy(User $authUser, User $user)
    {
        return $authUser->can(PermissionsEnum::DELETE_USERS->value) && !$user->hasRole([RolesEnum::SUPERADMIN->value, RolesEnum::ADMIN->value]);
    }
}
