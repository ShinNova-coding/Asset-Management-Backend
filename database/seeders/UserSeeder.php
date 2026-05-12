<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'employee_id' => 'EMP-001',
            'name' => 'System Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'),
            'role_id' => 1,
            'joined_date' => now(),
        ]);
    }
}