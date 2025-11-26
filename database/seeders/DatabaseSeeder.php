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
        // Run seeders in order: users -> user_accounts -> transactions
        $this->call([
            UserSeeder::class,
            UserAccountSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}