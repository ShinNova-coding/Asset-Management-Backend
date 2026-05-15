<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $admin = Role::create(['name' => 'Admin', 'guard_name' => 'sanctum']);
        $admin->givePermissionTo([
            'view-assets',
            'view-categories',
            'view-users',
            'manage-categories',
            'manage-assets',
            'manage-users',
        ]);

        $manager = Role::create(['name' => 'Manager', 'guard_name' => 'sanctum']);
        $manager->givePermissionTo([
            'view-assets',
            'view-categories',
            'view-users',
            'manage-categories',
            'manage-assets',
        ]);

        $employee = Role::create(['name' => 'Employee', 'guard_name' => 'sanctum']);
        $employee->givePermissionTo([
            'view-assets',
            'view-categories',
            'view-users',
        ]);
    }
}