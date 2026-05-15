<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::create(['name' => 'super-admin','guard_name' => 'sanctum']);
       $user= User::create([
            'employee_id' => 'EMP-001',
            'name' => 'System Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'),
            'joined_date' => now(),
        ]);
        $user->assignRole($superAdminRole);
    }
}