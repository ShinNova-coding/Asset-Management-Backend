<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'Admin',
            'Manager',
            'Employee',
        ];

        foreach ($roles as $role) {
            Role::create([
                'guard_name' => 'sanctum',
                'name' => $role
            ]);
        }
    }
}