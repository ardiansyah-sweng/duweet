<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    // Konstanta untuk nama akun yang sering digunakan
    private const BCA_AYAH = 'BCA Ayah';
    private const TAGIHAN_LISTRIK = 'Tagihan Listrik';
    private const TAGIHAN_INTERNET = 'Tagihan Internet dan Komunikasi';
    private const TAGIHAN_AIR_PDAM = 'Tagihan Air PDAM';
    private const RDN_MIRAE = 'RDN - Mirae';
    private const BIAYA_TRANSPORTASI = 'Transportation';
    private const BIAYA_EDUCATION = 'Education';
    private const BIAYA_HIBURAN_LIBURAN = 'Hiburan dan Liburan';
    private const BIAYA_FOOD_DINING = 'Food & Dining';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userAccountId = 1;
        $transactionDate = '2025-11-10 00:00:00';

        // Data transaksi yang akan dibuat
        $transactionData = [
            [
                'description' => 'Pembayaran tagihan listrik bulan November 2025',
                'debit_account' => self::TAGIHAN_LISTRIK,
                'credit_account' => self::BCA_AYAH,
                'amount' => 315055,
            ],
            [
                'description' => 'Pembayaran tagihan air PDAM bulan November 2025',
                'debit_account' => self::TAGIHAN_AIR_PDAM,
                'credit_account' => self::BCA_AYAH,
                'amount' => 93000,
            ],
            [
                'description' => 'Transfer dana investasi ke RDN Mirae',
                'debit_account' => self::RDN_MIRAE,
                'credit_account' => self::BCA_AYAH,
                'amount' => 100000000,
            ],
            [
                'description' => 'Pembayaran tagihan IndiHome bulan November 2025',
                'debit_account' => self::TAGIHAN_INTERNET,
                'credit_account' => self::BCA_AYAH,
                'amount' => 290157,
            ],
            [
                'description' => 'Bayar ongkir Kaos Ronin',
                'debit_account' => self::BIAYA_TRANSPORTASI,
                'credit_account' => self::BCA_AYAH,
                'amount' => 9000,
            ],
            [
                'description' => 'Biaya Bensin Brio KRKB Gembiraloka KK BCA 08-10-2025',
                'debit_account' => self::BIAYA_TRANSPORTASI,
                'credit_account' => self::BCA_AYAH,
                'amount' => 364000,
            ],
            [
                'description' => 'Biaya Github Copilot bulanan $10 x 16883 KKBCA 04-10-2025',
                'debit_account' => self::BIAYA_EDUCATION,
                'credit_account' => self::BCA_AYAH,
                'amount' => 168337,
            ],
            [
                'description' => 'Biaya langganan Netflix bulan November 2025 KKBCA 09-10-2025',
                'debit_account' => self::BIAYA_HIBURAN_LIBURAN,
                'credit_account' => self::BCA_AYAH,
                'amount' => 65000,
            ],
            [
                'description' => 'Biaya makan keluarga dengan mas Heri Maulana di Restoran Klatea Yk KKBC 08-10-2025',
                'debit_account' => self::BIAYA_FOOD_DINING,
                'credit_account' => self::BCA_AYAH,
                'amount' => 533720,
            ],
        ];

        // Proses setiap transaksi
        foreach ($transactionData as $data) {
            $this->createDoubleEntryTransaction(
                $userAccountId,
                $transactionDate,
                $data['description'],
                $data['debit_account'],
                $data['credit_account'],
                $data['amount']
            );
        }
    }

    /**
     * Membuat transaksi double entry (debit & kredit)
     */
    private function createDoubleEntryTransaction(
        int $userAccountId,
        string $transactionDate,
        string $description,
        string $debitAccountName,
        string $creditAccountName,
        int $amount
    ): void {
        // Generate UUID untuk grup transaksi
        $transactionGroupId = Str::uuid()->toString();

        // Ambil ID financial accounts
        $debitAccountId = $this->getFinancialAccountId($debitAccountName);
        $creditAccountId = $this->getFinancialAccountId($creditAccountName);

        $transactions = [
            // Transaksi Debit
            [
                'transaction_group_id' => $transactionGroupId,
                'user_account_id' => $userAccountId,
                'financial_account_id' => $debitAccountId,
                'entry_type' => 'debit',
                'amount' => $amount,
                'balance_effect' => 'increase',
                'description' => $description,
                'is_balance' => false,
                'created_at' => $transactionDate,
                'updated_at' => $transactionDate,
            ],
            // Transaksi Kredit
            [
                'transaction_group_id' => $transactionGroupId,
                'user_account_id' => $userAccountId,
                'financial_account_id' => $creditAccountId,
                'entry_type' => 'kredit',
                'amount' => $amount,
                'balance_effect' => 'decrease',
                'description' => $description . ' (pembayaran dari ' . $creditAccountName . ')',
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

        // Update balance
        $this->updateFinancialAccountBalances($transactions);

        $this->command->info("Transaction group created with ID: {$transactionGroupId}");
        $this->command->info("Total transactions created: " . count($transactions));
    }

    /**
     * Ambil ID financial account berdasarkan nama
     */
    private function getFinancialAccountId(string $accountName): int
    {
        return DB::table(config('db_tables.financial_account', 'financial_accounts'))
            ->where('name', $accountName)
            ->value('id');
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
