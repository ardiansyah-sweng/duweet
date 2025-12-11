<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
<<<<<<< HEAD
use Database\Seeders\SampleTotalsSeeder;
=======
use App\Models\User;
>>>>>>> 704974a8edd2f12696008b0f7dd219ec55e5e922

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
<<<<<<< HEAD
        // Delegate to the SampleTotalsSeeder which creates 4 users and their accounts
        $this->call([SampleTotalsSeeder::class]);
=======
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
            FinancialAccountSeeder::class,
            TransactionSeeder::class,
        ]);
>>>>>>> 704974a8edd2f12696008b0f7dd219ec55e5e922
    }
}