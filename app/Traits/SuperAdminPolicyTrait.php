<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\RolesEnum;
use App\Models\User;

trait SuperAdminPolicyTrait
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole(RolesEnum::SUPERADMIN->value)) {
            return true;
        }

        return null;
    }
}
