<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\User;
use App\Models\Assignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $asset = Asset::where('status', 'available')->first();

        if (!$user || !$asset) {
            $this->command->warn("Please seed Users and Assets first before seeding Assignments!");
            return;
        }

        DB::transaction(function () use ($user, $asset) {
            
            $asset->update(['status' => 'assigned']);

            Assignment::create([
                'asset_id'      => $asset->asset_id,
                'employee_id'   => $user->employee_id,
                'assigned_date' => now()->format('Y-m-d'),
                'status'        => 'active',
            ]);
        });

        $this->command->info("Assignment seeded successfully!");
    }
}