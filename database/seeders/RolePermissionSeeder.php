<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('permissions')->truncate();
        $permissions = [];
        foreach (PermissionsEnum::cases() as $permission) {
            $permissions[] = ['name' => $permission->value, 'guard_name' => config('auth.defaults.guard')];
        }

        Permission::insert($permissions);

        Schema::enableForeignKeyConstraints();

        Role::create(['name' => RolesEnum::SUPERADMIN->value, 'guard_name' => config('auth.defaults.guard')]);
        Role::create(['name' => RolesEnum::ADMIN->value, 'guard_name' => config('auth.defaults.guard')])
            ->syncPermissions(collect($permissions)->pluck('name')->toArray());
    }
}
