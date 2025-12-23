<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
{
        //User::factory(10)->create();

        // Seed accounts with real world data
        $this->call([
            FinancialAccountSeeder::class,
            UserSeeder::class,
            UserAccountSeeder::class,
            //AccountSeeder::class,
            //FinancialAccountSeeder::class,
            //TransactionSeeder::class,
            //FinancialAccountSeeder::class,
            //UserFinancialAccountSeeder::class,
            //TransactionSeeder::class,
            //AccountSeeder::class,
        ]);
    }
}