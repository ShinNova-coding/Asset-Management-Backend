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
            'view-dashboard',

            'view-assets',
            'create-assets',
            'update-assets',
            'delete-assets',

            'view-categories',
            'create-categories',
            'update-categories',
            'delete-categories',
            
            'view-users',
            'create-users',
            'update-users',
            'delete-users',
            
            'view-assignments',
            'create-assignments',
            'update-assignments',
            'delete-assignments',

            'view-maintenances',
            'create-maintenances',
            'update-maintenances',
            'delete-maintenances',

            'get-notifications',

            'create-asset-requests',
            'approve-asset-requests',
            'approve-asset-requests',
            'cancel-asset-requests',

            'create-maintenance-requests',
            'approve-maintenance-requests',
            'cancel-maintenance-requests',
            'update-maintenance-requests',
            
        ]);

        $manager = Role::create(['name' => 'Manager', 'guard_name' => 'sanctum']);
        $manager->givePermissionTo([
            'view-dashboard',
            'view-assets',
            'create-assets',
            'update-assets',
            'delete-assets',

            'view-categories',
            'create-categories',
            'update-categories',
            'delete-categories',

            'view-assignments',
            'create-assignments',
            'update-assignments',
            'delete-assignments',

            'view-maintenances',
            'create-maintenances',
            'update-maintenances',
            'delete-maintenances',

            'get-notifications',

            'create-asset-requests',
            'create-maintenance-requests',

        
        ]);

        $employee = Role::create(['name' => 'Employee', 'guard_name' => 'sanctum']);
        $employee->givePermissionTo([
            'view-assets',

            'view-categories',

            'view-assignments',
            'create-assignments',
            'update-assignments',

            'view-maintenances',
            'create-maintenances',

            'get-notifications',
            'create-asset-requests',
            'create-maintenance-requests',
        ]);
    }
}