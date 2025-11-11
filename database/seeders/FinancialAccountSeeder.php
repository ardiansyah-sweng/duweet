<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Constants\FinancialAccountColumns as C;

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

        $table = config('db_tables.financial_account');

        // Truncate the table
        DB::table($table)->truncate();

        // Re-enable foreign key checks
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $now = now();

        // 10 example financial accounts. Adjust balances/types as needed.
        $accounts = [
            // Root asset accounts
            [C::PARENT_ID => null, C::NAME => 'Cash', C::TYPE => 'AS', C::BALANCE => 5000000, C::INITIAL_BALANCE => 5000000, C::IS_GROUP => false, C::DESCRIPTION => 'On-hand cash', C::IS_ACTIVE => true, C::SORT_ORDER => 1, C::LEVEL => 0],
            [C::PARENT_ID => null, C::NAME => 'Bank', C::TYPE => 'AS', C::BALANCE => 15000000, C::INITIAL_BALANCE => 15000000, C::IS_GROUP => true, C::DESCRIPTION => 'Bank accounts group', C::IS_ACTIVE => true, C::SORT_ORDER => 2, C::LEVEL => 0],
            [C::PARENT_ID => null, C::NAME => 'Investment', C::TYPE => 'AS', C::BALANCE => 2000000, C::INITIAL_BALANCE => 2000000, C::IS_GROUP => false, C::DESCRIPTION => 'Investments', C::IS_ACTIVE => true, C::SORT_ORDER => 3, C::LEVEL => 0],

            // Children of Bank
            // We'll insert them with parent_id null first, then update parent relationship by querying inserted ids below.
            [C::PARENT_ID => 0, C::NAME => 'BCA Savings', C::TYPE => 'AS', C::BALANCE => 7000000, C::INITIAL_BALANCE => 7000000, C::IS_GROUP => false, C::DESCRIPTION => 'BCA savings account', C::IS_ACTIVE => true, C::SORT_ORDER => 1, C::LEVEL => 1],
            [C::PARENT_ID => 0, C::NAME => 'BNI Current', C::TYPE => 'AS', C::BALANCE => 8000000, C::INITIAL_BALANCE => 8000000, C::IS_GROUP => false, C::DESCRIPTION => 'BNI current account', C::IS_ACTIVE => true, C::SORT_ORDER => 2, C::LEVEL => 1],

            // Liabilities
            [C::PARENT_ID => null, C::NAME => 'Credit Card', C::TYPE => 'LI', C::BALANCE => -2500000, C::INITIAL_BALANCE => -2500000, C::IS_GROUP => false, C::DESCRIPTION => 'Credit card payable', C::IS_ACTIVE => true, C::SORT_ORDER => 1, C::LEVEL => 0],
            [C::PARENT_ID => null, C::NAME => 'Loan', C::TYPE => 'LI', C::BALANCE => -5000000, C::INITIAL_BALANCE => -5000000, C::IS_GROUP => false, C::DESCRIPTION => 'Outstanding loan', C::IS_ACTIVE => true, C::SORT_ORDER => 2, C::LEVEL => 0],

            // Income and Expense placeholder accounts
            [C::PARENT_ID => null, C::NAME => 'Income', C::TYPE => 'IN', C::BALANCE => 0, C::INITIAL_BALANCE => 0, C::IS_GROUP => true, C::DESCRIPTION => 'Income accounts group', C::IS_ACTIVE => true, C::SORT_ORDER => 1, C::LEVEL => 0],
            [C::PARENT_ID => null, C::NAME => 'Expense', C::TYPE => 'EX', C::BALANCE => 0, C::INITIAL_BALANCE => 0, C::IS_GROUP => true, C::DESCRIPTION => 'Expense accounts group', C::IS_ACTIVE => true, C::SORT_ORDER => 2, C::LEVEL => 0],
        ];

        // Insert root-level accounts and capture inserted ids to assign children properly
        $inserted = [];
        foreach ($accounts as $i => $acc) {
            // If parent_id is 0 it means "child of Bank"; defer for now
            if ($acc[C::PARENT_ID] === 0) {
                // push to deferred list
                $inserted['deferred'][] = $acc;
                continue;
            }

            $id = DB::table($table)->insertGetId(array_merge($acc, [C::CREATED_AT => $now, C::UPDATED_AT => $now]));
            $inserted['roots'][] = ['id' => $id, 'name' => $acc[C::NAME]];
        }

        // Find the 'Bank' root id to attach children
        $bank = DB::table($table)->where(C::NAME, 'Bank')->first();
        if ($bank && isset($inserted['deferred'])) {
            foreach ($inserted['deferred'] as $childAcc) {
                $childAcc[C::PARENT_ID] = $bank->id;
                DB::table($table)->insert(array_merge($childAcc, [C::CREATED_AT => $now, C::UPDATED_AT => $now]));
            }
        }
    }
}
