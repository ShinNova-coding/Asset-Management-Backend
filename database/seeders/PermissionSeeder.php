<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [

            'manage-categories',
            'view-categories',
            'create-categories',
            'update-categories',
            'delete-categories',

            'manage-assets',
            'view-assets',
            'create-assets',
            'update-assets',
            'delete-assets',

            'manage-users',
            'view-users',
            'create-users',
            'update-users',
            'delete-users',

            'manage-roles',
            'view-roles',
            'create-roles',
            'update-roles',
            'delete-roles',

            'manage-permissions',
            'view-permissions',
            'create-permissions',
            'update-permissions',
            'delete-permissions',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::create([
                'name' => $permission,
                'guard_name' => 'sanctum',
            ]);
        }
    }
}