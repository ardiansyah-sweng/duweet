<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            FinancialAccountSeeder::class,
            UserSeeder::class,
            UserAccountSeeder::class,
            // AccountSeeder::class,
        ]);
    }
}