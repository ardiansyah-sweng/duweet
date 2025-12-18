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
        // Create a test user only if not already exists
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'email' => 'test@example.com',
                'name' => 'Test User',
            ]);
        }

        // Run seeders in order: users -> user_accounts -> financial_accounts -> transactions
        $this->call([
            UserSeeder::class,
            UserAccountSeeder::class,
            //AccountSeeder::class,
            FinancialAccountSeeder::class,
            UserFinancialAccountSeeder::class,
            TransactionSeeder::class,
            //AccountSeeder::class,
        ]);
    }
}