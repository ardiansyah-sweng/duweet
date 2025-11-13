<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\FinancialAccount;
use App\Models\UserFinancialAccount;
use Illuminate\Support\Facades\DB;    

class UserFinancialAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan FK sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        UserFinancialAccount::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $userIds = DB::table('users')->pluck('id');
        $financialAccountIds = DB::table('financial_accounts')->where('is_group', false)->pluck('id');

        $userFinancialAccounts = [];
        foreach ($userIds as $userId) {
            foreach ($financialAccountIds as $accountId) {
                $userFinancialAccounts[] = [
                    'id_user' => $userId,
                    'financial_account_id' => $accountId,
                    'balance' => 1000000,
                    'initial_balance' => 1000000,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

DB::table('user_financial_accounts')->insert($userFinancialAccounts);

    }
}