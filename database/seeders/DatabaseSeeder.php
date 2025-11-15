<?php

namespace Database\Seeders;

<<<<<<< HEAD
use App\Models\FinancialAccount;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAccount;
=======
>>>>>>> f69f6a334e79a0c91b6090aee470fb63b59926ce
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
<<<<<<< HEAD
        User::factory(10)->create();
        UserAccount::factory(15)->create();
        
        FinancialAccount::factory(20)->create();
        FinancialAccount::factory(5)->group()->create();
        
        Transaction::factory(100)->create();
=======
        $this->call([
            UserSeeder::class,
            UserAccountSeeder::class,
            //AccountSeeder::class,
        ]);
>>>>>>> f69f6a334e79a0c91b6090aee470fb63b59926ce
    }
}