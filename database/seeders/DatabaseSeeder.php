<?php

namespace Database\Seeders;

use App\Models\FinancialAccount;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAccount;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        UserAccount::factory(15)->create();
        
        FinancialAccount::factory(20)->create();
        FinancialAccount::factory(5)->group()->create();
        
        Transaction::factory(100)->create();
    }
}