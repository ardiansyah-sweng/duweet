<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\UserAccount;
use App\Models\FinancialAccount;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan FK sementara supaya bisa truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Transaction::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ambil user account pertama
        $userAccounts = DB::table('user_accounts')->pluck('id');
        $financialAccounts = DB::table('financial_accounts')->where('is_group', false)->pluck('id');

        $transactions = [];
        foreach ($userAccounts as $i => $userAccountId) {
            foreach ($financialAccounts as $financialAccountId) {
                $transactions[] = [
                    'user_account_id' => $userAccountId,
                    'financial_account_id' => $financialAccountId,
                    'entry_type' => 'debit',
                    'amount' => 250000,
                    'balance_effect' => 'increase',
                    'description' => 'Pembelian peralatan kantor',
                    'is_balance' => false,
                    'transaction_group_id' => \Illuminate\Support\Str::uuid(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $transactions[] = [
                    'user_account_id' => $userAccountId,
                    'financial_account_id' => $financialAccountId,
                    'entry_type' => 'credit',
                    'amount' => 250000,
                    'balance_effect' => 'decrease',
                    'description' => 'Pembayaran hutang ke supplier',
                    'is_balance' => false,
                    'transaction_group_id' => \Illuminate\Support\Str::uuid(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('transactions')->insert($transactions);

    }
}
