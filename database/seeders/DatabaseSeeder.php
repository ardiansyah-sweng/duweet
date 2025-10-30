<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a predictable test user plus additional random users for test coverage
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // create additional users to reach ~10 users total for broader testing
        User::factory(9)->create();

        // Seed accounts first so transactions have accounts to reference
        $this->call([
            AccountSeeder::class,
        ]);

        // Seed transactions after accounts and users exist
        $this->call([
            TransactionSeeder::class,
        ]);
    }
}
