<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dry Food
        $category = Category::where('name', 'Dry Food')->first();
        $productName = [
            'Crunchy Delight',
            'NutriBites Supreme',
            'Purrfection Crisps',
            'Whisker Crunch Nuggets',
            'Prime Paws Crunch+',
        ];

        foreach ($productName as $name) {
            $category->products()->create([
                'name' => $name,
                'slug' => Str::slug($name),
                'price' => rand(10000, 100000),
                'image' => fake()->image('storage/app/public/image', 640, 480, null, false),
            ]);
        }

        // Wet Food
        $category = Category::where('name', 'Wet Food')->first();
        $productName = [
            'Gourmet Gravy Bliss',
            'Divine Delicacies Pouch',
            'Fishy Feast Medley',
            'Savory Selections Can Cuisine',
            'Tender Temptations in Sauce',
        ];

        foreach ($productName as $name) {
            $category->products()->create([
                'name' => $name,
                'slug' => Str::slug($name),
                'price' => rand(10000, 100000),
                'image' => fake()->image('storage/app/public/image', 640, 480, null, false),
            ]);
        }

        // Wet Food
        $category = Category::where('name', 'Raw Food')->first();
        $productName = [
            'RawRevolution Carnivore Blend',
            'Natures Nourish Raw Medley',
            'Instinctive Instinct Raw Bites',
            'Primal Palate Raw Mix',
            'Pawsome Protein Power Raw Feast',
        ];

        foreach ($productName as $name) {
            $category->products()->create([
                'name' => $name,
                'slug' => Str::slug($name),
                'price' => rand(10000, 100000),
                'image' => fake()->image('storage/app/public/image', 640, 480, null, false),
            ]);
        }
    }
}
