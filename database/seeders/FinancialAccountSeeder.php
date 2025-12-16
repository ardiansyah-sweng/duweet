<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Constants\FinancialAccountColumns as AccountColumns;
use App\Models\FinancialAccount;

class FinancialAccountSeeder extends Seeder
{
    public function run(): void
    {
        $table = config('db_tables.financial_account', 'financial_accounts');

        // Disable foreign key checks to allow delete (SQLite compatible)
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }
        
        DB::table($table)->delete();
        
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        DB::table($table)->insert([
            [
                AccountColumns::NAME             => 'Cash',
                AccountColumns::TYPE             => 'AS',
                AccountColumns::BALANCE          => 0,
                AccountColumns::INITIAL_BALANCE  => 0,
                AccountColumns::IS_GROUP         => false,
                AccountColumns::DESCRIPTION      => 'Cash on hand',
                AccountColumns::IS_ACTIVE        => true,
                AccountColumns::SORT_ORDER       => 1,
                AccountColumns::LEVEL            => 0,
                AccountColumns::PARENT_ID        => null,
            ],
            [
                AccountColumns::NAME             => 'Bank BCA',
                AccountColumns::TYPE             => 'AS',
                AccountColumns::BALANCE          => 0,
                AccountColumns::INITIAL_BALANCE  => 0,
                AccountColumns::IS_GROUP         => false,
                AccountColumns::DESCRIPTION      => 'Bank account for transactions',
                AccountColumns::IS_ACTIVE        => true,
                AccountColumns::SORT_ORDER       => 2,
                AccountColumns::LEVEL            => 0,
                AccountColumns::PARENT_ID        => null,
            ],
            [
                AccountColumns::NAME             => 'Expense: Food & Drinks',
                AccountColumns::TYPE             => 'EX',
                AccountColumns::BALANCE          => 0,
                AccountColumns::INITIAL_BALANCE  => 0,
                AccountColumns::IS_GROUP         => false,
                AccountColumns::DESCRIPTION      => 'Daily food & beverage expenses',
                AccountColumns::IS_ACTIVE        => true,
                AccountColumns::SORT_ORDER       => 3,
                AccountColumns::LEVEL            => 0,
                AccountColumns::PARENT_ID        => null,
            ],
        ]);

        // Optionally create additional randomized accounts via factory
        FinancialAccount::factory()->count(7)->create();
    }
}