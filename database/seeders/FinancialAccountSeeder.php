<?php

namespace Database\Seeders;

use App\Constants\FinancialAccountColumns;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinancialAccountSeeder extends Seeder
{
    public function run(): void
    {
        $table = config('db_tables.financial_account', 'financial_accounts');

        $dbDriver = DB::getDriverName();

        if ($dbDriver !== 'sqlite') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        DB::table($table)->delete();

        if ($dbDriver !== 'sqlite') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        DB::table($table)->insert([

            [
                FinancialAccountColumns::NAME            => 'Cash',
                FinancialAccountColumns::TYPE            => 'AS',
                FinancialAccountColumns::BALANCE         => 0,
                FinancialAccountColumns::INITIAL_BALANCE => 0,
                FinancialAccountColumns::IS_GROUP        => false,
                FinancialAccountColumns::DESCRIPTION     => 'Cash on hand',
                FinancialAccountColumns::IS_ACTIVE       => true,
                FinancialAccountColumns::SORT_ORDER      => 1,
                FinancialAccountColumns::LEVEL           => 0,
                FinancialAccountColumns::PARENT_ID       => null,
            ],

            [
                FinancialAccountColumns::NAME            => 'Bank BCA',
                FinancialAccountColumns::TYPE            => 'AS',
                FinancialAccountColumns::BALANCE         => 0,
                FinancialAccountColumns::INITIAL_BALANCE => 0,
                FinancialAccountColumns::IS_GROUP        => false,
                FinancialAccountColumns::DESCRIPTION     => 'Bank account for transactions',
                FinancialAccountColumns::IS_ACTIVE       => true,
                FinancialAccountColumns::SORT_ORDER      => 2,
                FinancialAccountColumns::LEVEL           => 0,
                FinancialAccountColumns::PARENT_ID       => null,
            ],

            [
                FinancialAccountColumns::NAME            => 'Expense: Food & Drinks',
                FinancialAccountColumns::TYPE            => 'EX',
                FinancialAccountColumns::BALANCE         => 0,
                FinancialAccountColumns::INITIAL_BALANCE => 0,
                FinancialAccountColumns::IS_GROUP        => false,
                FinancialAccountColumns::DESCRIPTION     => 'Daily food & beverage expenses',
                FinancialAccountColumns::IS_ACTIVE       => true,
                FinancialAccountColumns::SORT_ORDER      => 3,
                FinancialAccountColumns::LEVEL           => 0,
                FinancialAccountColumns::PARENT_ID       => null,
            ],


            [
                FinancialAccountColumns::NAME            => 'Income: Salary',
                FinancialAccountColumns::TYPE            => 'IN',
                FinancialAccountColumns::BALANCE         => 0,
                FinancialAccountColumns::INITIAL_BALANCE => 0,
                FinancialAccountColumns::IS_GROUP        => false,
                FinancialAccountColumns::DESCRIPTION     => 'Monthly salary income',
                FinancialAccountColumns::IS_ACTIVE       => true,
                FinancialAccountColumns::SORT_ORDER      => 4,
                FinancialAccountColumns::LEVEL           => 0,
                FinancialAccountColumns::PARENT_ID       => null,
            ],
        ]);
    }
}
