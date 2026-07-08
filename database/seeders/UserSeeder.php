<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Membuat akun demo untuk masing-masing role.
     * Kredensial login lengkap ada di README.md.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', Role::ADMIN)->first();
        $staffRole = Role::where('name', Role::STAFF)->first();
        $managerRole = Role::where('name', Role::MANAGER)->first();

        User::updateOrCreate(
            ['email' => 'admin@telkomsel.test'],
            [
                'name' => 'Admin Inventaris',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@telkomsel.test'],
            [
                'name' => 'Staff Gudang',
                'password' => Hash::make('password'),
                'role_id' => $staffRole->id,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@telkomsel.test'],
            [
                'name' => 'Manager Operasional',
                'password' => Hash::make('password'),
                'role_id' => $managerRole->id,
                'is_active' => true,
            ]
        );
    }
}
