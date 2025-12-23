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

        // Jalankan seeders berurutan: users -> user_accounts -> financial_accounts -> user_financial_accounts -> transactions
        $this->call([
            FinancialAccountSeeder::class,
            UserSeeder::class,
            UserAccountSeeder::class,
            FinancialAccountSeeder::class, // sebelumnya AccountSeeder / FinancialAccountSeeder, kita pakai yang konsisten
            UserFinancialAccountSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
