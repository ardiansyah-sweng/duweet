<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\FinancialAccount;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $transactionTable = config('db_tables.transaction', 'transactions');

        // Ensure we have income accounts
        $incomeCount = FinancialAccount::where('type', 'IN')->count();
        if ($incomeCount === 0) {
            $this->command->warn('No INCOME accounts found. Run AccountSeeder first.');
        }

        // Optional: clear existing transactions
        DB::table($transactionTable)->truncate();

        // Create sample transactions
        Transaction::factory()->count(100)->create();

        $this->command->info('Transaction seeder completed! Created 100 income credit transactions.');
    }
}
