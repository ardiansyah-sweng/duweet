<?php

namespace Database\Seeders;

use App\Constants\TransactionColumns;
use App\Models\Transaction;
use App\Models\UserAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing user accounts
        $userAccounts = UserAccount::all();
        
        if ($userAccounts->isEmpty()) {
            $this->command->warn('No user accounts found. Please run UserAccountSeeder first.');
            return;
        }

        // Check if financial_accounts table exists and has data
        $financialAccountsExist = DB::getSchemaBuilder()->hasTable('financial_accounts');
        $financialAccountIds = [];
        
        if ($financialAccountsExist) {
            $financialAccountIds = DB::table('financial_accounts')
                ->where('is_group', false) // Only leaf accounts can have transactions
                ->pluck('id')
                ->toArray();
        }

        if (empty($financialAccountIds)) {
            $this->command->warn('No financial accounts found. Please run AccountSeeder first.');
            return;
        }

        $this->command->info('Creating transactions for different periods...');

        // Define periods to seed
        $periods = [
            ['2025-11-01', '2025-11-14', 2, 'November 2025'],
            ['2025-10-01', '2025-10-31', 2, 'October 2025'],
            ['2025-09-01', '2025-09-30', 2, 'September 2025'],
            ['2025-08-01', '2025-08-31', 2, 'August 2025'],
            ['2025-06-01', '2025-07-31', 2, 'June-July 2025'],
            ['2024-10-01', '2024-12-31', 2, 'Q4 2024'],
        ];

        // Create transactions for each user account and period
        foreach ($userAccounts as $userAccount) {
            foreach ($periods as [$startDate, $endDate, $count, $label]) {
                $this->createTransactionsForPeriod(
                    $userAccount->id,
                    $financialAccountIds,
                    $startDate,
                    $endDate,
                    $count,
                    $label
                );
            }
        }

        $totalTransactions = Transaction::count();
        $this->command->info("TransactionSeeder completed! Created {$totalTransactions} transactions.");
    }

    /**
     * Create transactions for a specific period
     */
    private function createTransactionsForPeriod(
        int $userAccountId,
        array $financialAccountIds,
        string $startDate,
        string $endDate,
        int $count,
        string $label
    ): void {
        for ($i = 0; $i < $count; $i++) {
            // Create paired debit-credit transactions
            $groupId = (string) Str::uuid();
            $amount = rand(50000, 2000000); // 50k - 2jt
            $randomDate = $this->randomDateBetween($startDate, $endDate);
            
            $fromAccountId = $financialAccountIds[array_rand($financialAccountIds)];
            $toAccountId = $financialAccountIds[array_rand($financialAccountIds)];
            
            // Ensure different accounts
            while ($toAccountId === $fromAccountId && count($financialAccountIds) > 1) {
                $toAccountId = $financialAccountIds[array_rand($financialAccountIds)];
            }

            // Debit transaction (from account) - Using Factory
            Transaction::factory()
                ->withGroupId($groupId)
                ->debit()
                ->decrease()
                ->balanced()
                ->onDate($randomDate)
                ->create([
                    TransactionColumns::USER_ACCOUNT_ID => $userAccountId,
                    TransactionColumns::FINANCIAL_ACCOUNT_ID => $fromAccountId,
                    TransactionColumns::AMOUNT => $amount,
                    TransactionColumns::DESCRIPTION => "Transfer to account #{$toAccountId} - {$label}",
                ]);

            // Credit transaction (to account) - Using Factory
            Transaction::factory()
                ->withGroupId($groupId)
                ->credit()
                ->increase()
                ->balanced()
                ->onDate($randomDate)
                ->create([
                    TransactionColumns::USER_ACCOUNT_ID => $userAccountId,
                    TransactionColumns::FINANCIAL_ACCOUNT_ID => $toAccountId,
                    TransactionColumns::AMOUNT => $amount,
                    TransactionColumns::DESCRIPTION => "Transfer from account #{$fromAccountId} - {$label}",
                ]);
        }

        $this->command->info("  ✓ Created {$count} transaction pairs for {$label}");
    }

    /**
     * Generate random date between two dates
     */
    private function randomDateBetween(string $startDate, string $endDate): string
    {
        $startTimestamp = strtotime($startDate);
        $endTimestamp = strtotime($endDate);
        $randomTimestamp = rand($startTimestamp, $endTimestamp);
        
        return date('Y-m-d H:i:s', $randomTimestamp);
    }
}
