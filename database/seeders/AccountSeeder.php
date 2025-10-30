<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Constants\FinancialAccountColumns;

class AccountSeeder extends Seeder
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
        
        // Resolve the accounts table name from config to match migrations
    $accountsTable = config('db_tables.financial_account', 'financial_accounts');

        // Truncate the table (safe because we disabled foreign keys above)
        DB::table($accountsTable)->truncate();
        
        // Re-enable foreign key checks
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

    // Load account data from file
    $accountsData = include database_path('data/accounts_data.php');
        
        // Process the hierarchical data
        foreach ($accountsData as $rootAccount) {
            $this->insertAccount($rootAccount, null, $accountsTable);
        }
    }

    /**
     * Insert account and its children recursively
     */
    private function insertAccount(array $accountData, ?int $parentId = null, string $accountsTable = 'accounts'): int
    {
        // Prepare account data for insertion
        $account = [
            FinancialAccountColumns::PARENT_ID => $parentId,
            FinancialAccountColumns::NAME => $accountData['name'],
            FinancialAccountColumns::TYPE => $accountData['type'],
            FinancialAccountColumns::BALANCE => $accountData['initial_balance'] ?? 0,
            FinancialAccountColumns::INITIAL_BALANCE => $accountData['initial_balance'] ?? 0,
            FinancialAccountColumns::IS_GROUP => $accountData['is_group'] ?? false,
            FinancialAccountColumns::DESCRIPTION => $accountData['description'] ?? null,
            FinancialAccountColumns::IS_ACTIVE => $accountData['is_active'] ?? true,
            FinancialAccountColumns::SORT_ORDER => $accountData['sort_order'] ?? 0,
            FinancialAccountColumns::LEVEL => $accountData['level'] ?? 0,
            FinancialAccountColumns::CREATED_AT => now(),
            FinancialAccountColumns::UPDATED_AT => now(),
        ];

    // Insert the account and get the ID
    $accountId = DB::table($accountsTable)->insertGetId($account);

        // Process children if they exist
        if (isset($accountData['children']) && is_array($accountData['children'])) {
            foreach ($accountData['children'] as $childData) {
                $this->insertAccount($childData, $accountId, $accountsTable);
            }
        }

        return $accountId;
    }
}
