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
        if (! User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'email' => 'test@example.com',
                'name'  => 'Test User',
            ]);
        }

        // Seeder order:
        $this->call([
            UserSeeder::class,
            AccountSeeder::class,
            UserFinancialAccountSeeder::class,
            UserAccountSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
 // ...existing code...