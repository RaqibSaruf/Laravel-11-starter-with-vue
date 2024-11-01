<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Test User',
            'email' => 'raqibul.dev@gamil.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
        ])->assignRole(RolesEnum::SUPERADMIN->value);
    }
}
