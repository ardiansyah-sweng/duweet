<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Constants\UserFinancialAccountColumns;

class UserFinancialAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Disable FK checks
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        
        DB::table(config('db_tables.user_financial_account'))->truncate();
        
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Ambil ID dari tabel user_accounts (bukan users!)
        $userAccountIds = DB::table(config('db_tables.user_account'))->pluck('id');

        // Ambil financial account yg bukan group
        $financialAccountIds = DB::table(config('db_tables.financial_account'))
            ->where('is_group', false)
            ->pluck('id');

        $records = [];

        foreach ($userAccountIds as $uaId) {
            foreach ($financialAccountIds as $faId) {
                $records[] = [
                    UserFinancialAccountColumns::USER_ACCOUNT_ID => $uaId,
                    UserFinancialAccountColumns::FINANCIAL_ACCOUNT_ID => $faId,
                    UserFinancialAccountColumns::INITIAL_BALANCE => 1_000_000,
                    UserFinancialAccountColumns::BALANCE => 1_000_000,
                    UserFinancialAccountColumns::IS_ACTIVE => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table(config('db_tables.user_financial_account'))->insert($records);
    }
}
