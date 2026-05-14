<?php

namespace Database\Seeders;
use App\Models\Category;
use Illuminate\Database\Seeder;


class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Laptop', 'Monitor', 'Mobile Phone', 'Tablet', 'Office Chair'];

        foreach ($categories as $cat) {
            \App\Models\Category::create(['name' => $cat]);
        }
    }
}
