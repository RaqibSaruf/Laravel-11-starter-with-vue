<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\RolesEnum;
use App\Models\User;

class Policy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole([RolesEnum::SUPERADMIN->value, RolesEnum::ADMIN->value])) {
            return true;
        }

        return null;
    }
}
