<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Constants\TransactionColumns;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $table = config('db_tables.transaction', 'transactions');

        // Truncate table
        DB::table($table)->truncate();

        // Get sample IDs from database
        $userAccounts = DB::table('user_accounts')->pluck('id')->take(3)->toArray();
        $financialAccounts = DB::table('financial_accounts')->pluck('id')->take(10)->toArray();

        if (empty($userAccounts) || empty($financialAccounts)) {
            $this->command->warn('⚠️  No user accounts or financial accounts found. Run UserAccountSeeder and AccountSeeder first.');
            return;
        }

        $transactions = [];
        
        // Transaction 1: User 1 - Deposit Cash (Kas bertambah)
        $groupId1 = Str::uuid()->toString();
        $transactions[] = [
            TransactionColumns::USER_ACCOUNT_ID => $userAccounts[0],
            TransactionColumns::FINANCIAL_ACCOUNT_ID => $financialAccounts[0], // Kas
            TransactionColumns::TRANSACTION_GROUP_ID => $groupId1,
            TransactionColumns::ENTRY_TYPE => 'debit',
            TransactionColumns::AMOUNT => 1000000,
            TransactionColumns::BALANCE_EFFECT => 'increase',
            TransactionColumns::DESCRIPTION => 'Setoran kas awal',
            TransactionColumns::IS_BALANCE => false,
            TransactionColumns::CREATED_AT => now()->subDays(10),
            TransactionColumns::UPDATED_AT => now()->subDays(10),
        ];
        $transactions[] = [
            TransactionColumns::USER_ACCOUNT_ID => $userAccounts[0],
            TransactionColumns::FINANCIAL_ACCOUNT_ID => $financialAccounts[1], // Modal
            TransactionColumns::TRANSACTION_GROUP_ID => $groupId1,
            TransactionColumns::ENTRY_TYPE => 'credit',
            TransactionColumns::AMOUNT => 1000000,
            TransactionColumns::BALANCE_EFFECT => 'increase',
            TransactionColumns::DESCRIPTION => 'Setoran kas awal',
            TransactionColumns::IS_BALANCE => false,
            TransactionColumns::CREATED_AT => now()->subDays(10),
            TransactionColumns::UPDATED_AT => now()->subDays(10),
        ];

        // Transaction 2: User 1 - Pembelian barang
        $groupId2 = Str::uuid()->toString();
        $transactions[] = [
            TransactionColumns::USER_ACCOUNT_ID => $userAccounts[0],
            TransactionColumns::FINANCIAL_ACCOUNT_ID => $financialAccounts[2], // Persediaan
            TransactionColumns::TRANSACTION_GROUP_ID => $groupId2,
            TransactionColumns::ENTRY_TYPE => 'debit',
            TransactionColumns::AMOUNT => 500000,
            TransactionColumns::BALANCE_EFFECT => 'increase',
            TransactionColumns::DESCRIPTION => 'Pembelian barang dagangan',
            TransactionColumns::IS_BALANCE => false,
            TransactionColumns::CREATED_AT => now()->subDays(9),
            TransactionColumns::UPDATED_AT => now()->subDays(9),
        ];
        $transactions[] = [
            TransactionColumns::USER_ACCOUNT_ID => $userAccounts[0],
            TransactionColumns::FINANCIAL_ACCOUNT_ID => $financialAccounts[0], // Kas
            TransactionColumns::TRANSACTION_GROUP_ID => $groupId2,
            TransactionColumns::ENTRY_TYPE => 'credit',
            TransactionColumns::AMOUNT => 500000,
            TransactionColumns::BALANCE_EFFECT => 'decrease',
            TransactionColumns::DESCRIPTION => 'Pembelian barang dagangan',
            TransactionColumns::IS_BALANCE => false,
            TransactionColumns::CREATED_AT => now()->subDays(9),
            TransactionColumns::UPDATED_AT => now()->subDays(9),
        ];

        // Transaction 3: User 1 - Penjualan
        $groupId3 = Str::uuid()->toString();
        $transactions[] = [
            TransactionColumns::USER_ACCOUNT_ID => $userAccounts[0],
            TransactionColumns::FINANCIAL_ACCOUNT_ID => $financialAccounts[0], // Kas
            TransactionColumns::TRANSACTION_GROUP_ID => $groupId3,
            TransactionColumns::ENTRY_TYPE => 'debit',
            TransactionColumns::AMOUNT => 750000,
            TransactionColumns::BALANCE_EFFECT => 'increase',
            TransactionColumns::DESCRIPTION => 'Penjualan barang',
            TransactionColumns::IS_BALANCE => false,
            TransactionColumns::CREATED_AT => now()->subDays(8),
            TransactionColumns::UPDATED_AT => now()->subDays(8),
        ];
        $transactions[] = [
            TransactionColumns::USER_ACCOUNT_ID => $userAccounts[0],
            TransactionColumns::FINANCIAL_ACCOUNT_ID => $financialAccounts[3], // Pendapatan
            TransactionColumns::TRANSACTION_GROUP_ID => $groupId3,
            TransactionColumns::ENTRY_TYPE => 'credit',
            TransactionColumns::AMOUNT => 750000,
            TransactionColumns::BALANCE_EFFECT => 'increase',
            TransactionColumns::DESCRIPTION => 'Penjualan barang',
            TransactionColumns::IS_BALANCE => false,
            TransactionColumns::CREATED_AT => now()->subDays(8),
            TransactionColumns::UPDATED_AT => now()->subDays(8),
        ];

        // Transaction 4: User 2 - Deposit
        if (isset($userAccounts[1])) {
            $groupId4 = Str::uuid()->toString();
            $transactions[] = [
                TransactionColumns::USER_ACCOUNT_ID => $userAccounts[1],
                TransactionColumns::FINANCIAL_ACCOUNT_ID => $financialAccounts[0],
                TransactionColumns::TRANSACTION_GROUP_ID => $groupId4,
                TransactionColumns::ENTRY_TYPE => 'debit',
                TransactionColumns::AMOUNT => 2000000,
                TransactionColumns::BALANCE_EFFECT => 'increase',
                TransactionColumns::DESCRIPTION => 'Setoran kas user 2',
                TransactionColumns::IS_BALANCE => false,
                TransactionColumns::CREATED_AT => now()->subDays(7),
                TransactionColumns::UPDATED_AT => now()->subDays(7),
            ];
            $transactions[] = [
                TransactionColumns::USER_ACCOUNT_ID => $userAccounts[1],
                TransactionColumns::FINANCIAL_ACCOUNT_ID => $financialAccounts[1],
                TransactionColumns::TRANSACTION_GROUP_ID => $groupId4,
                TransactionColumns::ENTRY_TYPE => 'credit',
                TransactionColumns::AMOUNT => 2000000,
                TransactionColumns::BALANCE_EFFECT => 'increase',
                TransactionColumns::DESCRIPTION => 'Setoran kas user 2',
                TransactionColumns::IS_BALANCE => false,
                TransactionColumns::CREATED_AT => now()->subDays(7),
                TransactionColumns::UPDATED_AT => now()->subDays(7),
            ];
        }

        // Transaction 5: User 1 - Bayar beban
        $groupId5 = Str::uuid()->toString();
        $transactions[] = [
            TransactionColumns::USER_ACCOUNT_ID => $userAccounts[0],
            TransactionColumns::FINANCIAL_ACCOUNT_ID => $financialAccounts[4], // Beban
            TransactionColumns::TRANSACTION_GROUP_ID => $groupId5,
            TransactionColumns::ENTRY_TYPE => 'debit',
            TransactionColumns::AMOUNT => 150000,
            TransactionColumns::BALANCE_EFFECT => 'increase',
            TransactionColumns::DESCRIPTION => 'Bayar beban listrik',
            TransactionColumns::IS_BALANCE => false,
            TransactionColumns::CREATED_AT => now()->subDays(6),
            TransactionColumns::UPDATED_AT => now()->subDays(6),
        ];
        $transactions[] = [
            TransactionColumns::USER_ACCOUNT_ID => $userAccounts[0],
            TransactionColumns::FINANCIAL_ACCOUNT_ID => $financialAccounts[0], // Kas
            TransactionColumns::TRANSACTION_GROUP_ID => $groupId5,
            TransactionColumns::ENTRY_TYPE => 'credit',
            TransactionColumns::AMOUNT => 150000,
            TransactionColumns::BALANCE_EFFECT => 'decrease',
            TransactionColumns::DESCRIPTION => 'Bayar beban listrik',
            TransactionColumns::IS_BALANCE => false,
            TransactionColumns::CREATED_AT => now()->subDays(6),
            TransactionColumns::UPDATED_AT => now()->subDays(6),
        ];

        // Transaction 6: User 1 - Balance transaction (penyesuaian)
        $groupId6 = Str::uuid()->toString();
        $transactions[] = [
            TransactionColumns::USER_ACCOUNT_ID => $userAccounts[0],
            TransactionColumns::FINANCIAL_ACCOUNT_ID => $financialAccounts[0],
            TransactionColumns::TRANSACTION_GROUP_ID => $groupId6,
            TransactionColumns::ENTRY_TYPE => 'debit',
            TransactionColumns::AMOUNT => 50000,
            TransactionColumns::BALANCE_EFFECT => 'increase',
            TransactionColumns::DESCRIPTION => 'Penyesuaian saldo kas',
            TransactionColumns::IS_BALANCE => true,
            TransactionColumns::CREATED_AT => now()->subDays(5),
            TransactionColumns::UPDATED_AT => now()->subDays(5),
        ];
        $transactions[] = [
            TransactionColumns::USER_ACCOUNT_ID => $userAccounts[0],
            TransactionColumns::FINANCIAL_ACCOUNT_ID => $financialAccounts[5],
            TransactionColumns::TRANSACTION_GROUP_ID => $groupId6,
            TransactionColumns::ENTRY_TYPE => 'credit',
            TransactionColumns::AMOUNT => 50000,
            TransactionColumns::BALANCE_EFFECT => 'increase',
            TransactionColumns::DESCRIPTION => 'Penyesuaian saldo kas',
            TransactionColumns::IS_BALANCE => true,
            TransactionColumns::CREATED_AT => now()->subDays(5),
            TransactionColumns::UPDATED_AT => now()->subDays(5),
        ];

        // Insert all transactions
        DB::table($table)->insert($transactions);

        $totalTransactions = count($transactions);
        $this->command->info("✅ {$totalTransactions} transactions seeded successfully!");
        $this->command->info("   - User accounts used: " . count($userAccounts));
        $this->command->info("   - Financial accounts used: " . count($financialAccounts));
        $this->command->info("   - Transaction groups created: 6");
    }
}
