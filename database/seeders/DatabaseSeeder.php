<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        // User::factory(10)->create();

        // Create test user only if it doesn't already exist.
        if (! User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        // Seed core entities: accounts and user_financial_accounts
        $this->call([
            AccountSeeder::class,
            UserFinancialAccountSeeder::class,
        ]);
        $this->call([
            UserSeeder::class,
            UserAccountSeeder::class,

        ]);
    }
}
