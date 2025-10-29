<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            // For MySQL
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        
        // Truncate the table
        DB::table('accounts')->truncate();
        
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
            $this->insertAccount($rootAccount);
        }
    }

    /**
     * Insert account and its children recursively
     */
    private function insertAccount(array $accountData, ?int $parentId = null): int
    {
        // Prepare account data for insertion
        $account = [
            'parent_id' => $parentId,
            'name' => $accountData['name'],
            'type' => $accountData['type'],
            'balance' => $accountData['initial_balance'] ?? 0,
            'initial_balance' => $accountData['initial_balance'] ?? 0,
            'is_group' => $accountData['is_group'] ?? false,
            'description' => $accountData['description'] ?? null,
            'is_active' => $accountData['is_active'] ?? true,
            'sort_order' => $accountData['sort_order'] ?? 0,
            'level' => $accountData['level'] ?? 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Insert the account and get the ID
        $accountId = DB::table('accounts')->insertGetId($account);

        // Process children if they exist
        if (isset($accountData['children']) && is_array($accountData['children'])) {
            foreach ($accountData['children'] as $childData) {
                $this->insertAccount($childData, $accountId);
            }
        }

        return $accountId;
    }
}
