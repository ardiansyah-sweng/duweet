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
            UserSeeder::class,
            UserAccountSeeder::class,
<<<<<<< HEAD
            //AccountSeeder::class,
            //FinancialAccountSeeder::class,
            //TransactionSeeder::class,
            //FinancialAccountSeeder::class,
            //UserFinancialAccountSeeder::class,
            //TransactionSeeder::class,
=======
            AccountSeeder::class,
            FinancialAccountSeeder::class,
            UserFinancialAccountSeeder::class,
            TransactionSeeder::class,
>>>>>>> f4526a8f92a989a5a26d0d4d84264f8649478a42
            //AccountSeeder::class,
        ]);
    }
}