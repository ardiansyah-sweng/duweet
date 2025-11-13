<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\UserAccount; 
use App\Constants\TransactionColumns;
use App\Constants\UserAccountColumns; 
use Carbon\Carbon;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $targetUsername = 'johndoe'; 
        
        $userAccount = UserAccount::where(UserAccountColumns::USERNAME, $targetUsername)->first();

        if (!$userAccount) {
            $this->command->error("User Account '{$targetUsername}' not found. Please run UserAccountSeeder first.");
            return;
        }

        $user = $userAccount->user; 
        
        $financialAccountId = DB::table('financial_accounts')->pluck('id')->first();
        if (!$financialAccountId) {
            $this->command->error("Financial Account not found. Run AccountSeeder first.");
            return;
        }
        
        $userAccountId = $userAccount->id;
        $transactions = [];
        $today = Carbon::now();

        // Periode Pengujian: Bulan ini
        $startTestDate = $today->copy()->startOfMonth(); 
        
        // --- Data Uji Transaksi (Skenario: Surplus) ---
        
        // 1. INCOME (Credit) - Total: 10,000,000
        $transactions[] = [
            TransactionColumns::USER_ACCOUNT_ID => $userAccountId,
            TransactionColumns::FINANCIAL_ACCOUNT_ID => $financialAccountId,
            TransactionColumns::TRANSACTION_GROUP_ID => Str::uuid()->toString(),
            TransactionColumns::ENTRY_TYPE => 'credit', // FINAL
            TransactionColumns::AMOUNT => 10000000,
            TransactionColumns::BALANCE_EFFECT => 'increase',
            TransactionColumns::DESCRIPTION => 'Gaji bulanan',
            TransactionColumns::IS_BALANCE => false,
            TransactionColumns::CREATED_AT => $startTestDate->copy()->addDays(5),
            TransactionColumns::UPDATED_AT => $startTestDate->copy()->addDays(5),
        ];

        // 2. EXPENSE (Debit) - Total: 3,500,000
        $transactions[] = [
            TransactionColumns::USER_ACCOUNT_ID => $userAccountId,
            TransactionColumns::FINANCIAL_ACCOUNT_ID => $financialAccountId,
            TransactionColumns::TRANSACTION_GROUP_ID => Str::uuid()->toString(),
            TransactionColumns::ENTRY_TYPE => 'debit', 
            TransactionColumns::AMOUNT => 2000000,
            TransactionColumns::BALANCE_EFFECT => 'decrease',
            TransactionColumns::DESCRIPTION => 'Bayar sewa',
            TransactionColumns::IS_BALANCE => false,
            TransactionColumns::CREATED_AT => $startTestDate->copy()->addDays(10),
            TransactionColumns::UPDATED_AT => $startTestDate->copy()->addDays(10),
        ];
        
        $transactions[] = [
            TransactionColumns::USER_ACCOUNT_ID => $userAccountId,
            TransactionColumns::FINANCIAL_ACCOUNT_ID => $financialAccountId,
            TransactionColumns::TRANSACTION_GROUP_ID => Str::uuid()->toString(),
            TransactionColumns::ENTRY_TYPE => 'debit', 
            TransactionColumns::AMOUNT => 1500000,
            TransactionColumns::BALANCE_EFFECT => 'decrease',
            TransactionColumns::DESCRIPTION => 'Belanja & Transportasi',
            TransactionColumns::IS_BALANCE => false,
            TransactionColumns::CREATED_AT => $startTestDate->copy()->addDays(15),
            TransactionColumns::UPDATED_AT => $startTestDate->copy()->addDays(15),
        ];

        $transactions[] = [
            TransactionColumns::USER_ACCOUNT_ID => $userAccountId,
            TransactionColumns::FINANCIAL_ACCOUNT_ID => $financialAccountId,
            TransactionColumns::TRANSACTION_GROUP_ID => Str::uuid()->toString(),
            TransactionColumns::ENTRY_TYPE => 'credit',
            TransactionColumns::AMOUNT => 500000,
            TransactionColumns::BALANCE_EFFECT => 'increase',
            TransactionColumns::DESCRIPTION => 'Bonus bulan lalu',
            TransactionColumns::IS_BALANCE => false,
            TransactionColumns::CREATED_AT => $startTestDate->copy()->subMonths(1)->addDays(1),
            TransactionColumns::UPDATED_AT => $startTestDate->copy()->subMonths(1)->addDays(1),
        ];

        DB::table('transactions')->insert($transactions);
    
    }
}