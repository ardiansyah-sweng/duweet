<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
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
            FinancialAccountSeeder::class,
            UserSeeder::class,
            UserAccountSeeder::class,
            FinancialAccountSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}