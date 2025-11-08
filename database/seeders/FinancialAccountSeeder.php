<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Enums\AccountType;

class FinancialAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // For SQLite - disable foreign key checks
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            // For MySQL
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        
        // Truncate the table
        DB::table('financial_accounts')->truncate();
        
        // Re-enable foreign key checks
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Get all expense accounts
        $expenseAccounts = $this->getExpenseAccounts();

        // Insert financial accounts for each expense account
        foreach ($expenseAccounts as $accountId) {
            DB::table('financial_accounts')->insert([
                'name' => "Expense Account - " . $accountId,
                    'type' => AccountType::EXPENSES->value,
                'account_id' => $accountId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Get all expense account IDs from accounts table
     */
    private function getExpenseAccounts(): array
    {
        return DB::table('accounts')
            ->where('type', 'EX')
            ->where('is_group', false)
            ->pluck('id')
            ->toArray();
    }
}