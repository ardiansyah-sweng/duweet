<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
<<<<<<< HEAD
        // Run seeders in order: users -> user_accounts -> transactions
        $this->call([
            UserSeeder::class,
            UserAccountSeeder::class,
            TransactionSeeder::class,
=======
        // Create a test user only if not already exists
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'email' => 'test@example.com',
                'name' => 'Test User',
            ]);
                // Create a test user only if not already exists
                if (!User::where('email', 'test@example.com')->exists()) {
                    User::factory()->create([
                        'email' => 'test@example.com',
                        'name' => 'Test User',
                    ]);
                }

                // Run seeders in order
                $this->call([
                    UserSeeder::class,
                    UserAccountSeeder::class,
                    FinancialAccountSeeder::class,
                    TransactionSeeder::class,
            FinancialAccountSeeder::class,
>>>>>>> origin/main
        ]);
    }
}