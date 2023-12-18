<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Insert with id is generated uuid
        $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
        DB::table('users')->insert([
            'id' => $uuid,
            'name' => 'Admin',
            'email' => 'admin@localhost',
            'password' => Hash::make('admin@localhost'),
        ]);

        // User Insert
        $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
        DB::table('users')->insert([
            'id' => $uuid,
            'name' => 'User',
            'email' => 'user@localhost',
            'password' => Hash::make('user@localhost'),
        ]);

        // Random User Insert
        for ($i = 0; $i < 10; $i++) {
            $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
            DB::table('users')->insert([
                'id' => $uuid,
                'name' => fake()->name(),
                'email' => fake()->email(),
                'password' => Hash::make('password123'),
            ]);
        }
    }
}
