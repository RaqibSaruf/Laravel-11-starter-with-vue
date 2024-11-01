<?php

declare(strict_types=1);

namespace App\Enums;

enum RolesEnum: string
{
    case SUPERADMIN = 'super-admin';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            static::SUPERADMIN => 'Super Admin',
            static::ADMIN => 'Admin',
        };
    }
}
