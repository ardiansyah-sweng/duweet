<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Constants\TransactionColumns;
use App\Constants\UserAccountColumns;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = DB::table('users')->get();
        
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Skipping transaction seeding.');
            return;
        }
        
        // Get financial accounts
        $financialAccounts = DB::table(config('db_tables.financial_account', 'financial_accounts'))
            ->where('is_group', false)
            ->limit(5)
            ->get();
        
        if ($financialAccounts->isEmpty()) {
            $this->command->warn('No financial accounts found. Skipping transaction seeding.');
            return;
        }
        
        $totalTransactions = 0;
        
        // Create transactions for EACH user
        foreach ($users as $user) {
            // Create or get user account for this user
            $userAccount = DB::table('user_accounts')
                ->where(UserAccountColumns::ID_USER, $user->id)
                ->first();
            
            if (!$userAccount) {
                // Create user account if not exists
                $userAccountId = DB::table('user_accounts')->insertGetId([
                    UserAccountColumns::ID_USER => $user->id,
                    UserAccountColumns::USERNAME => strtolower(str_replace(' ', '', $user->name)),
                    UserAccountColumns::EMAIL => $user->email,
                    UserAccountColumns::PASSWORD => bcrypt('password'),
                    UserAccountColumns::VERIFIED_AT => now(),
                    UserAccountColumns::IS_ACTIVE => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $userAccountId = $userAccount->id;
            }
            
            // Create random number of transactions per user (10-30 transactions)
            $transactionCount = rand(10, 30);
            $transactions = [];
            
            for ($i = 0; $i < $transactionCount; $i++) {
                $groupId = Str::uuid()->toString();
                $amount = rand(10000, 500000); // Random amount between 10k and 500k
                $financialAccount = $financialAccounts->random();
                
                // Debit entry
                $transactions[] = [
                    TransactionColumns::TRANSACTION_GROUP_ID => $groupId,
                    TransactionColumns::USER_ACCOUNT_ID => $userAccountId,
                    TransactionColumns::FINANCIAL_ACCOUNT_ID => $financialAccount->id,
                    TransactionColumns::ENTRY_TYPE => 'debit',
                    TransactionColumns::AMOUNT => $amount,
                    TransactionColumns::BALANCE_EFFECT => 'increase',
                    TransactionColumns::DESCRIPTION => 'Transaction for ' . $user->name . ' #' . ($i + 1) . ' - Debit',
                    TransactionColumns::IS_BALANCE => false,
                    'created_at' => now()->subDays(rand(0, 60)),
                    'updated_at' => now(),
                ];
                
                // Credit entry (matching debit)
                $transactions[] = [
                    TransactionColumns::TRANSACTION_GROUP_ID => $groupId,
                    TransactionColumns::USER_ACCOUNT_ID => $userAccountId,
                    TransactionColumns::FINANCIAL_ACCOUNT_ID => $financialAccounts->random()->id,
                    TransactionColumns::ENTRY_TYPE => 'credit',
                    TransactionColumns::AMOUNT => $amount,
                    TransactionColumns::BALANCE_EFFECT => 'decrease',
                    TransactionColumns::DESCRIPTION => 'Transaction for ' . $user->name . ' #' . ($i + 1) . ' - Credit',
                    TransactionColumns::IS_BALANCE => false,
                    'created_at' => now()->subDays(rand(0, 60)),
                    'updated_at' => now(),
                ];
            }
            
            // Insert transactions for this user
            DB::table('transactions')->insert($transactions);
            $totalTransactions += count($transactions);
            
            $this->command->info('Created ' . count($transactions) . ' transactions for user: ' . $user->name);
        }
        
        $this->command->info('Total: ' . $totalTransactions . ' sample transactions created for ' . $users->count() . ' users.');
    }
}
