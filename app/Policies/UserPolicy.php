<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use App\Models\User;
use App\Traits\SuperAdminPolicyTrait;

class UserPolicy
{
    use SuperAdminPolicyTrait;

    public function index(User $authUser)
    {
        return $authUser->can(PermissionsEnum::VIEWUSERS->value);
    }

    public function store(User $authUser)
    {
        return $authUser->can(PermissionsEnum::CREATEUSERS->value);
    }

    public function show(User $authUser, User $user)
    {
        if ($authUser->id !== $user->id && !$user->hasRole([RolesEnum::SUPERADMIN->value, RolesEnum::ADMIN->value])) {
            return false;
        }

        return $authUser->can(PermissionsEnum::VIEWUSERS->value);
    }

    public function update(User $authUser, User $user)
    {
        if ($authUser->id !== $user->id && !$user->hasRole([RolesEnum::SUPERADMIN->value, RolesEnum::ADMIN->value])) {
            return false;
        }

        return $authUser->can(PermissionsEnum::EDITUSERS->value);
    }

    public function destroy(User $authUser, User $user)
    {
        return $authUser->can(PermissionsEnum::DELETEUSERS->value) && !$user->hasRole([RolesEnum::SUPERADMIN->value, RolesEnum::ADMIN->value]);
    }
}
