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
        // Create multiple users for testing
        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ]);

        User::factory()->create([
            'name' => 'Bob Wilson',
            'email' => 'bob@example.com',
        ]);

        User::factory()->create([
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com',
        ]);

        User::factory()->create([
            'name' => 'Charlie Brown',
            'email' => 'charlie@example.com',
        ]);

        // Seed accounts with real world data
        $this->call([
            AccountSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
