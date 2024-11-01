<?php

declare(strict_types=1);

namespace App\Enums;

enum PermissionsEnum: string
{
    case CREATEUSERS = 'users.create';
    case EDITUSERS = 'users.edit';
    case VIEWUSERS = 'users.view';
    case DELETEUSERS = 'users.delete';

    case CREATEROLES = 'roles.create';
    case EDITROLES = 'roles.edit';
    case VIEWROLES = 'roles.view';
    case DELETEROLES = 'roles.delete';

    public function label(): string
    {
        return match ($this) {
            static::CREATEUSERS => 'Create Users',
            static::EDITUSERS => 'Edit Users',
            static::VIEWUSERS => 'View Users',
            static::DELETEUSERS => 'Delete Users',

            static::CREATEROLES => 'Create Roles',
            static::EDITROLES => 'Edit Roles',
            static::VIEWROLES => 'View Roles',
            static::DELETEROLES => 'Delete Roles',
        };
    }
}
