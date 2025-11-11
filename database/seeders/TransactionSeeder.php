<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // === TRANSAKSI 1: Pembayaran Tagihan Listrik ===
        $this->createElectricityBillTransaction();
        
        // === TRANSAKSI 2: Pembayaran Tagihan Air PDAM ===
        $this->createWaterBillTransaction();
    }
    
    /**
     * Transaksi pembayaran tagihan listrik
     */
    private function createElectricityBillTransaction(): void
    {
        // Generate UUID untuk grup transaksi
        $transactionGroupId = Str::uuid()->toString();
        
        // Tanggal transaksi: 10/11/2025
        $transactionDate = '2025-11-10 00:00:00';
        
        // User account ID (ID yang sebenarnya dari database)
        $userAccountId = 1;
        
        // Cari ID financial account berdasarkan nama
        $bcaAyahId = DB::table(config('db_tables.financial_account', 'financial_accounts'))
            ->where('name', 'BCA Ayah')
            ->value('id');
            
        $tagihanListrikId = DB::table(config('db_tables.financial_account', 'financial_accounts'))
            ->where('name', 'Tagihan Listrik')
            ->value('id');
        
        $transactions = [
            // Transaksi Debit - Tagihan Listrik
            [
                'transaction_group_id' => $transactionGroupId,
                'user_account_id' => $userAccountId,
                'financial_account_id' => $tagihanListrikId,
                'entry_type' => 'debit',
                'amount' => 321000,
                'balance_effect' => 'increase',
                'description' => 'Pembayaran tagihan listrik bulan November 2025',
                'is_balance' => false,
                'created_at' => $transactionDate,
                'updated_at' => $transactionDate,
            ],
            
            // Transaksi Kredit - BCA Ayah
            [
                'transaction_group_id' => $transactionGroupId,
                'user_account_id' => $userAccountId,
                'financial_account_id' => $bcaAyahId,
                'entry_type' => 'kredit',
                'amount' => 321000,
                'balance_effect' => 'decrease',
                'description' => 'Pembayaran tagihan listrik dari rekening BCA Ayah',
                'is_balance' => false,
                'created_at' => $transactionDate,
                'updated_at' => $transactionDate,
            ],
        ];

        // Insert transaksi ke database
        foreach ($transactions as $transaction) {
            DB::table(config('db_tables.transaction', 'transactions'))->insert($transaction);
            
            $this->command->info("Created transaction: {$transaction['entry_type']} - {$transaction['description']} (Amount: Rp " . number_format($transaction['amount']) . ")");
        }
        
        // Update balance di financial accounts
        $this->updateFinancialAccountBalances($transactions);
        
        $this->command->info("Electricity bill transaction group created with ID: {$transactionGroupId}");
        $this->command->info("Total electricity transactions created: " . count($transactions));
    }
    
    /**
     * Transaksi pembayaran tagihan air PDAM
     */
    private function createWaterBillTransaction(): void
    {
        // Generate UUID untuk grup transaksi baru
        $transactionGroupId = Str::uuid()->toString();
        
        // Tanggal transaksi: 10/11/2025
        $transactionDate = '2025-11-10 00:00:00';
        
        // User account ID (ID yang sebenarnya dari database)
        $userAccountId = 1;
        
        // Cari ID financial account berdasarkan nama
        $bcaAyahId = DB::table(config('db_tables.financial_account', 'financial_accounts'))
            ->where('name', 'BCA Ayah')
            ->value('id');
            
        $tagihanAirId = DB::table(config('db_tables.financial_account', 'financial_accounts'))
            ->where('name', 'Tagihan Air PDAM')
            ->value('id');
        
        $transactions = [
            // Transaksi Debit - Tagihan Air PDAM
            [
                'transaction_group_id' => $transactionGroupId,
                'user_account_id' => $userAccountId,
                'financial_account_id' => $tagihanAirId,
                'entry_type' => 'debit',
                'amount' => 90000,
                'balance_effect' => 'increase',
                'description' => 'Pembayaran tagihan air PDAM bulan November 2025',
                'is_balance' => false,
                'created_at' => $transactionDate,
                'updated_at' => $transactionDate,
            ],
            
            // Transaksi Kredit - BCA Ayah
            [
                'transaction_group_id' => $transactionGroupId,
                'user_account_id' => $userAccountId,
                'financial_account_id' => $bcaAyahId,
                'entry_type' => 'kredit',
                'amount' => 90000,
                'balance_effect' => 'decrease',
                'description' => 'Pembayaran tagihan air PDAM dari rekening BCA Ayah',
                'is_balance' => false,
                'created_at' => $transactionDate,
                'updated_at' => $transactionDate,
            ],
        ];

        // Insert transaksi ke database
        foreach ($transactions as $transaction) {
            DB::table(config('db_tables.transaction', 'transactions'))->insert($transaction);
            
            $this->command->info("Created transaction: {$transaction['entry_type']} - {$transaction['description']} (Amount: Rp " . number_format($transaction['amount']) . ")");
        }
        
        // Update balance di financial accounts
        $this->updateFinancialAccountBalances($transactions);
        
        $this->command->info("Water bill transaction group created with ID: {$transactionGroupId}");
        $this->command->info("Total water transactions created: " . count($transactions));
    }
    
    /**
     * Update balance di financial accounts berdasarkan transaksi
     */
    private function updateFinancialAccountBalances(array $transactions): void
    {
        foreach ($transactions as $transaction) {
            $currentBalance = DB::table(config('db_tables.financial_account', 'financial_accounts'))
                ->where('id', $transaction['financial_account_id'])
                ->value('balance');
                
            if ($transaction['balance_effect'] === 'increase') {
                $newBalance = $currentBalance + $transaction['amount'];
            } else {
                $newBalance = $currentBalance - $transaction['amount'];
            }
            
            DB::table(config('db_tables.financial_account', 'financial_accounts'))
                ->where('id', $transaction['financial_account_id'])
                ->update(['balance' => $newBalance]);
                
            $accountName = DB::table(config('db_tables.financial_account', 'financial_accounts'))
                ->where('id', $transaction['financial_account_id'])
                ->value('name');
                
            $this->command->info("Updated balance for {$accountName}: " . number_format($newBalance));
        }
    }
}
