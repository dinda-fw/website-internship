<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => Role::ADMIN, 'label' => 'Administrator'],
            ['name' => Role::STAFF, 'label' => 'Staff Gudang'],
            ['name' => Role::MANAGER, 'label' => 'Manager'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }
    }
}
