<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // PermissionSeeder.php

public function run(): void
{
    $permissions = [
        // User & Role
        'manage-users',
        'manage-roles',

        // Assets
        'view-assets',
        'create-assets',
        'edit-assets',
        'delete-assets',

        // Categories & Brands
        'manage-categories',
        'manage-brands',
        'manage-locations',

        // Maintenance
        'view-maintenance',
        'update-maintenance',
        'view-dashboard',

        // Employee Side (Mobile App အတွက်ပါ ကြိုစဉ်းစားထားတာ)
        'request-assets',
        'view-my-assets',
    ];

    foreach ($permissions as $permission) {
        Permission::create(['name' => $permission]);
    }
}
}
