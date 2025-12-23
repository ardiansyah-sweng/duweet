<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinancialAccount;
use App\Constants\FinancialAccountColumns;

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
                FinancialAccountColumns::NAME             => 'Cash',
                FinancialAccountColumns::TYPE             => 'AS',
                FinancialAccountColumns::BALANCE          => 0,
                FinancialAccountColumns::INITIAL_BALANCE  => 0,
                FinancialAccountColumns::IS_GROUP         => false,
                FinancialAccountColumns::DESCRIPTION      => 'Cash on hand',
                FinancialAccountColumns::IS_ACTIVE        => true,
                FinancialAccountColumns::SORT_ORDER       => 1,
                FinancialAccountColumns::LEVEL            => 0,
                FinancialAccountColumns::PARENT_ID        => null,
            ],
            [
                FinancialAccountColumns::NAME             => 'Bank BCA',
                FinancialAccountColumns::TYPE             => 'AS',
                FinancialAccountColumns::BALANCE          => 0,
                FinancialAccountColumns::INITIAL_BALANCE  => 0,
                FinancialAccountColumns::IS_GROUP         => false,
                FinancialAccountColumns::DESCRIPTION      => 'Bank account for transactions',
                FinancialAccountColumns::IS_ACTIVE        => true,
                FinancialAccountColumns::SORT_ORDER       => 2,
                FinancialAccountColumns::LEVEL            => 0,
                FinancialAccountColumns::PARENT_ID        => null,
            ],
            [
                FinancialAccountColumns::NAME             => 'Expense: Food & Drinks',
                FinancialAccountColumns::TYPE             => 'EX',
                FinancialAccountColumns::BALANCE          => 0,
                FinancialAccountColumns::INITIAL_BALANCE  => 0,
                FinancialAccountColumns::IS_GROUP         => false,
                FinancialAccountColumns::DESCRIPTION      => 'Daily food & beverage expenses',
                FinancialAccountColumns::IS_ACTIVE        => true,
                FinancialAccountColumns::SORT_ORDER       => 3,
                FinancialAccountColumns::LEVEL            => 0,
                FinancialAccountColumns::PARENT_ID        => null,
            ],
        ]);

        FinancialAccount::create([
            FinancialAccountColumns::PARENT_ID       => null,
            FinancialAccountColumns::NAME            => 'Bank BNI',
            FinancialAccountColumns::TYPE            => 'AS',
            FinancialAccountColumns::BALANCE         => 100000,
            FinancialAccountColumns::INITIAL_BALANCE => 100000,
            FinancialAccountColumns::DESCRIPTION     => 'Rekening Bank BNI',
            FinancialAccountColumns::IS_GROUP        => false,
            FinancialAccountColumns::IS_ACTIVE       => true,
            FinancialAccountColumns::SORT_ORDER      => 1,
            FinancialAccountColumns::LEVEL           => 0,
        ]);

        FinancialAccount::create([
            FinancialAccountColumns::PARENT_ID       => null,
            FinancialAccountColumns::NAME            => 'Bank BRI',
            FinancialAccountColumns::TYPE            => 'AS',
            FinancialAccountColumns::BALANCE         => 1500000,
            FinancialAccountColumns::INITIAL_BALANCE => 1500000,
            FinancialAccountColumns::DESCRIPTION     => 'Rekening Bank BRI',
            FinancialAccountColumns::IS_GROUP        => false,
            FinancialAccountColumns::IS_ACTIVE       => true,
            FinancialAccountColumns::SORT_ORDER      => 2,
            FinancialAccountColumns::LEVEL           => 0,
        ]);

        FinancialAccount::create([
            FinancialAccountColumns::PARENT_ID       => null,
            FinancialAccountColumns::NAME            => 'Bank BCA',
            FinancialAccountColumns::TYPE            => 'AS',
            FinancialAccountColumns::BALANCE         => 5000000,
            FinancialAccountColumns::INITIAL_BALANCE => 5000000,
            FinancialAccountColumns::DESCRIPTION     => 'Rekening utama BCA',
            FinancialAccountColumns::IS_GROUP        => false,
            FinancialAccountColumns::IS_ACTIVE       => false,
            FinancialAccountColumns::SORT_ORDER      => 2,
            FinancialAccountColumns::LEVEL           => 0,
        ]);
    }
}