<?php

declare(strict_types=1);

namespace App\Enums;

enum PermissionsEnum: string
{
    case CREATE_USERS = 'create.users';
    case EDIT_USERS = 'edit.users';
    case VIEW_USERS = 'view.users';
    case DELETE_USERS = 'delete.users';

    case CREATE_ROLES = 'create.roles';
    case EDIT_ROLES = 'edit.roles';
    case VIEW_ROLES = 'view.roles';
    case DELETE_ROLES = 'delete.roles';
}
