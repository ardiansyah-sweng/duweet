<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test users without using factory (to avoid missing columns)
        User::create([
            'name' => 'Demo User',
            'first_name' => 'Demo',
            'last_name' => 'User',
            'email' => 'demo_full@duweet.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Test User 2',
            'first_name' => 'Test',
            'last_name' => 'User Two',
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Test User 3',
            'first_name' => 'Test',
            'last_name' => 'User Three',
            'email' => 'user3@example.com',
            'password' => Hash::make('password'),
        ]);

        // Run other seeders in order: user_accounts -> transactions
        $this->call([
            UserAccountSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}