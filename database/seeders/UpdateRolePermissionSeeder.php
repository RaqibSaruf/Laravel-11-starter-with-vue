<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=UpdateRolePermissionSeeder.
     */
    public function run(): void
    {
        $permissions = [];
        foreach (PermissionsEnum::cases() as $permission) {
            $permissions[] = ['name' => $permission->value, 'guard_name' => config('auth.defaults.guard')];
        }

        $collection = collect($permissions);

        Permission::whereNotIn('name', $collection->pluck('name')->toArray())->delete();

        $existingRecords = Permission::whereIn('name', $collection->pluck('name')->toArray())->pluck('name')->toArray();

        $valuesToAdd = $collection->whereNotIn('name', $existingRecords)->toArray();

        Permission::insert($valuesToAdd);

        $adminRole = Role::where('name', RolesEnum::ADMIN->value)
            ->where('guard_name', config('auth.defaults.guard'))
            ->first();
        if ($adminRole) {
            $adminRole->syncPermissions(collect($permissions)->pluck('name')->toArray());
        }
    }
}
