<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            "name" => "Dry Food",
            "slug" => "dry-food"
        ]);
        Category::create([
            "name" => "Wet Food",
            "slug" => "wet-food"
        ]);
        Category::create([
            "name" => "Raw Food",
            "slug" => "raw-food"
        ]);

    }
}
