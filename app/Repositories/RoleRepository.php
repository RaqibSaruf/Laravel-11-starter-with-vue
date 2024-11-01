<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\RolesEnum;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class RoleRepository implements Repository
{
    public function paginate(Request $request)
    {
        return Role::select('id', 'name')
            ->search($request)
            ->sort($request)
            ->paginate($request->input('limit', config('common.pagi_limit')));
    }

    public function getList(): Collection
    {
        $defaultRoles = collect(RolesEnum::cases())->map(fn ($role) => $role->value)->toArray();

        return Role::select('id', 'name')->whereNotIn('name', $defaultRoles)
            ->sort('name', 'asc')
            ->get();
    }
}
