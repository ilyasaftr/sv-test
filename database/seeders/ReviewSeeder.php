<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // get all product
        $products = Product::all();

        // get all user
        $users = User::all();

        // loop all product
        foreach ($products as $product) {
            // loop random from 1 to 10 for each product
            for ($i = 0; $i < rand(1, 10); $i++) {
                // get random user but exclude data with name admin
                $user = $users->except('name', 'Admin')->random();

                // create review
                $product->reviews()->create([
                    'user_id' => $user->id,
                    'rating' => rand(1, 5),
                    'title' => fake()->sentence(),
                    'content' => fake()->paragraphs(3, true),
                ]);
            }
        }

    }
}
