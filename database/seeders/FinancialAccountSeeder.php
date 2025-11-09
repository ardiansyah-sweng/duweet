<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\FinancialAccount;
use App\Constants\FinancialAccountColumns;

class FinancialAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $table = config('db_tables.financial_account', 'financial_accounts');

        // Disable foreign key checks for truncate
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        DB::table($table)->truncate();

        // Explicit sample data (declared) for financial_accounts
        $sample = [
            [
                'name' => 'Cash on Hand',
                'type' => 'AS',
                'initial_balance' => 1000000,
                'balance' => 1000000,
                'is_group' => false,
                'is_active' => true,
                'sort_order' => 1,
                'level' => 0,
                'description' => 'Main cash account',
                'children' => [
                    [
                        'name' => 'Petty Cash',
                        'type' => 'AS',
                        'initial_balance' => 50000,
                        'balance' => 50000,
                        'is_group' => false,
                        'is_active' => true,
                        'sort_order' => 1,
                        'level' => 1,
                        'description' => 'Small change',
                    ],
                    [
                        'name' => 'Cash in Safe',
                        'type' => 'AS',
                        'initial_balance' => 200000,
                        'balance' => 200000,
                        'is_group' => false,
                        'is_active' => true,
                        'sort_order' => 2,
                        'level' => 1,
                        'description' => 'Cash stored in safe',
                    ],
                ],
            ],
            [
                'name' => 'Bank Accounts',
                'type' => 'AS',
                'initial_balance' => 5000000,
                'balance' => 5000000,
                'is_group' => true,
                'is_active' => true,
                'sort_order' => 2,
                'level' => 0,
                'description' => 'Company bank accounts',
                'children' => [
                    [
                        'name' => 'BCA - Checking',
                        'type' => 'AS',
                        'initial_balance' => 3000000,
                        'balance' => 3000000,
                        'is_group' => false,
                        'is_active' => true,
                        'sort_order' => 1,
                        'level' => 1,
                        'description' => 'BCA primary account',
                    ],
                    [
                        'name' => 'Mandiri - Savings',
                        'type' => 'AS',
                        'initial_balance' => 2000000,
                        'balance' => 2000000,
                        'is_group' => false,
                        'is_active' => true,
                        'sort_order' => 2,
                        'level' => 1,
                        'description' => 'Mandiri savings',
                    ],
                ],
            ],
            [
                'name' => 'Accounts Receivable',
                'type' => 'AS',
                'initial_balance' => 0,
                'balance' => 0,
                'is_group' => false,
                'is_active' => true,
                'sort_order' => 3,
                'level' => 0,
                'description' => 'Amounts due from customers',
            ],
            [
                'name' => 'Suppliers',
                'type' => 'LI',
                'initial_balance' => 0,
                'balance' => 0,
                'is_group' => false,
                'is_active' => true,
                'sort_order' => 4,
                'level' => 0,
                'description' => 'Accounts payable to suppliers',
            ],
        ];

        // Insert sample data recursively
        foreach ($sample as $root) {
            $rootInsert = [
                FinancialAccountColumns::PARENT_ID => null,
                FinancialAccountColumns::NAME => $root['name'],
                FinancialAccountColumns::TYPE => $root['type'],
                FinancialAccountColumns::INITIAL_BALANCE => $root['initial_balance'] ?? 0,
                FinancialAccountColumns::BALANCE => $root['balance'] ?? 0,
                FinancialAccountColumns::IS_GROUP => $root['is_group'] ?? false,
                FinancialAccountColumns::IS_ACTIVE => $root['is_active'] ?? true,
                FinancialAccountColumns::SORT_ORDER => $root['sort_order'] ?? 0,
                FinancialAccountColumns::LEVEL => $root['level'] ?? 0,
                FinancialAccountColumns::DESCRIPTION => $root['description'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $rootId = DB::table($table)->insertGetId($rootInsert);

            if (!empty($root['children']) && is_array($root['children'])) {
                foreach ($root['children'] as $child) {
                    $childInsert = [
                        FinancialAccountColumns::PARENT_ID => $rootId,
                        FinancialAccountColumns::NAME => $child['name'],
                        FinancialAccountColumns::TYPE => $child['type'],
                        FinancialAccountColumns::INITIAL_BALANCE => $child['initial_balance'] ?? 0,
                        FinancialAccountColumns::BALANCE => $child['balance'] ?? 0,
                        FinancialAccountColumns::IS_GROUP => $child['is_group'] ?? false,
                        FinancialAccountColumns::IS_ACTIVE => $child['is_active'] ?? true,
                        FinancialAccountColumns::SORT_ORDER => $child['sort_order'] ?? 0,
                        FinancialAccountColumns::LEVEL => $child['level'] ?? 1,
                        FinancialAccountColumns::DESCRIPTION => $child['description'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $childId = DB::table($table)->insertGetId($childInsert);

                    // Optionally insert grandchildren if declared
                    if (!empty($child['children']) && is_array($child['children'])) {
                        foreach ($child['children'] as $grand) {
                            $grandInsert = [
                                FinancialAccountColumns::PARENT_ID => $childId,
                                FinancialAccountColumns::NAME => $grand['name'],
                                FinancialAccountColumns::TYPE => $grand['type'],
                                FinancialAccountColumns::INITIAL_BALANCE => $grand['initial_balance'] ?? 0,
                                FinancialAccountColumns::BALANCE => $grand['balance'] ?? 0,
                                FinancialAccountColumns::IS_GROUP => $grand['is_group'] ?? false,
                                FinancialAccountColumns::IS_ACTIVE => $grand['is_active'] ?? true,
                                FinancialAccountColumns::SORT_ORDER => $grand['sort_order'] ?? 0,
                                FinancialAccountColumns::LEVEL => $grand['level'] ?? 2,
                                FinancialAccountColumns::DESCRIPTION => $grand['description'] ?? null,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];

                            DB::table($table)->insert($grandInsert);
                        }
                    }
                }
            }
        }

        // Re-enable foreign key checks
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
