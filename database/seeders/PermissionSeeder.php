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

            'view-dashboard',
            
            'manage-assignments',
            'view-assignments',
            'create-assignments',
            'update-assignments',
            'delete-assignments',

            'manage-maintenances',
            'view-maintenances',
            'create-maintenances',  
            'update-maintenances',
            'delete-maintenances',

            'get-notifications',

            'create-asset-requests',
            'approve-asset-requests',
            'cancel-asset-requests',

            'manage-maintenance-requests',
            'view-maintenance-requests',
            'create-maintenance-requests',
            'approve-maintenance-requests',
            'cancel-maintenance-requests',
            'update-maintenance-requests',

            'manage-expenses',
            'view-expenses',
            'create-expenses',
            'update-expenses',
            'delete-expenses',

            'manage-expense-requests',
            'view-expense-requests',
            'create-expense-requests',
            'approve-expense-requests',
            'cancel-expense-requests',
            'update-expense-requests',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::create([
                'name' => $permission,
                'guard_name' => 'sanctum',
            ]);
        }
    }
}