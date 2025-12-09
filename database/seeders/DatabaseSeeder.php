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

        $this->call([
            UserSeeder::class,
            UserAccountSeeder::class,
<<<<<<< HEAD
            AccountSeeder::class,
            TransactionSeeder::class, 
=======
            //AccountSeeder::class,
            FinancialAccountSeeder::class,
>>>>>>> 1ddf2b86ee702e9d70eeccf8ccd250a7abec4494
        ]);
    }
}
