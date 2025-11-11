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
    private const SAHAM_BBRI = 'Saham BBRI';
    private const SAHAM_RALS = 'Saham RALS';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userAccountId = DB::table('user_accounts')->first()->id;
        $transactionDate = '2025-11-10 00:00:00';
        $newTransactionDate = '2025-11-11 00:00:00'; // Untuk transaksi RALS

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
            [
                'description' => 'Pembelian saham BBRI 40 lot @ Rp 3,950 + biaya Rp 33,700',
                'debit_account' => self::SAHAM_BBRI,
                'credit_account' => self::RDN_MIRAE,
                'amount' => 15833700, // (40 lot × 100 shares × Rp 3,950) + Rp 33,700
                'is_stock_transaction' => true,
                'stock_details' => [
                    'symbol' => 'BBRI.JK',
                    'quantity' => 4000, // 40 lot × 100 shares
                    'price_per_share' => 3950,
                    'fees' => 33700,
                ],
            ],
            [
                'description' => 'Penjualan Saham RALS sebanyak 360 lot di harga Rp 450 total biaya penjualan Rp 63,072',
                'debit_account' => self::RDN_MIRAE,
                'credit_account' => self::SAHAM_RALS,
                'amount' => 16137000, // (360 lot × 100 shares × Rp 450) - Rp 63,072 = Rp 16,200,000 - Rp 63,072
                'is_stock_transaction' => true,
                'stock_details' => [
                    'symbol' => 'RALS',
                    'quantity' => 36000, // 360 lot × 100 shares
                    'price_per_share' => 450,
                    'fees' => 73072,
                    'transaction_type' => 'sell',
                ],
            ],
        ];

        // Proses setiap transaksi
        foreach ($transactionData as $index => $data) {
            // Gunakan tanggal berbeda untuk transaksi RALS (transaksi terakhir)
            $currentTransactionDate = (count($transactionData) - 1 === $index) ? $newTransactionDate : $transactionDate;
            
            $this->createDoubleEntryTransaction(
                $userAccountId,
                $currentTransactionDate,
                $data['description'],
                $data['debit_account'],
                $data['credit_account'],
                $data['amount'],
                $data['is_stock_transaction'] ?? false,
                $data['stock_details'] ?? null
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
        int $amount,
        bool $isStockTransaction = false,
        ?array $stockDetails = null
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

        // Jika ini transaksi saham, update assets table
        if ($isStockTransaction && $stockDetails) {
            $this->updateStockAsset($stockDetails, $amount);
        }

        $this->command->info("Transaction group created with ID: {$transactionGroupId}");
        $this->command->info("Total transactions created: " . count($transactions));
    }

    /**
     * Update stock asset dengan average price calculation (buy/sell)
     */
    private function updateStockAsset(array $stockDetails, int $totalAmount): void
    {
        $newQuantity = $stockDetails['quantity']; // dalam shares
        $newPricePerShare = $stockDetails['price_per_share'];
        $transactionType = $stockDetails['transaction_type'] ?? 'buy';
        $symbol = $stockDetails['symbol'];
        
        // Tentukan nama account berdasarkan symbol
        $accountName = $symbol === 'RALS' ? self::SAHAM_RALS : self::SAHAM_BBRI;
        
        // Cari financial account ID untuk saham
        $financialAccountId = $this->getFinancialAccountId($accountName);
        
        // Ambil data asset yang sudah ada berdasarkan financial_account_id
        $existingAsset = DB::table('assets')
            ->where('financial_account_id', $financialAccountId)
            ->where('is_sold', false)
            ->orderBy('acquisition_date', 'desc')
            ->first();

        if ($existingAsset) {
            if ($transactionType === 'sell') {
                // Untuk penjualan, update sell quantity
                $existingSellQty = (int) $existingAsset->sell_quantity;
                $totalSellLots = ($existingSellQty + $newQuantity) / 100; // convert to lots
                
                // Mark sebagai sold jika semua asset dijual
                $totalBuyLots = (int) $existingAsset->buy_quantity;
                $isSold = $totalSellLots >= $totalBuyLots;
                
                DB::table('assets')
                    ->where('id', $existingAsset->id)
                    ->update([
                        'sell_quantity' => $existingSellQty + $newQuantity,
                        'sold_price' => $newPricePerShare,
                        'sold_date' => now()->format('Y-m-d'),
                        'is_sold' => $isSold,
                        'updated_at' => now(),
                    ]);

                // Update financial account balance
                if ($isSold) {
                    // Jika dijual seluruhnya, balance jadi 0
                    DB::table('financial_accounts')
                        ->where('id', $financialAccountId)
                        ->update([
                            'balance' => 0,
                            'updated_at' => now(),
                        ]);
                } else {
                    // Jika dijual sebagian, hitung sisa value
                    $remainingLots = $totalBuyLots - $totalSellLots;
                    $remainingShares = $remainingLots * 100;
                    $avgCostPrice = (float) $existingAsset->bought_price;
                    $remainingValue = $remainingShares * $avgCostPrice;
                    
                    DB::table('financial_accounts')
                        ->where('id', $financialAccountId)
                        ->update([
                            'balance' => $remainingValue,
                            'updated_at' => now(),
                        ]);
                }

                $this->command->info("Updated {$accountName} asset (Asset ID: {$existingAsset->id}):");
                $this->command->info("  Sold: " . ($newQuantity/100) . " lots @ Rp " . number_format($newPricePerShare));
                $this->command->info("  Status: " . ($isSold ? 'Fully Sold' : 'Partially Sold'));
                if ($isSold) {
                    $this->command->info("  ✅ Account balance set to 0 (fully sold)");
                }
            } else {
                // Untuk pembelian (existing logic)
                // Asset table menyimpan dalam LOTS, tapi calculation perlu dalam SHARES
                $existingLots = (int) $existingAsset->buy_quantity;
                $existingShares = $existingLots * 100; // convert lots to shares
                $existingAvgPrice = (float) $existingAsset->bought_price;
                $existingTotalValue = $existingShares * $existingAvgPrice;

                $totalShares = $existingShares + $newQuantity;
                $totalValue = $existingTotalValue + $totalAmount;
                $newAveragePrice = $totalValue / $totalShares;
                $totalLots = $totalShares / 100; // convert back to lots for storage

                // Update assets table
                DB::table('assets')
                    ->where('id', $existingAsset->id)
                    ->update([
                        'buy_quantity' => $totalLots,
                        'bought_price' => $newAveragePrice,
                        'updated_at' => now(),
                    ]);

                $this->command->info("Updated {$accountName} asset (Asset ID: {$existingAsset->id}):");
                $this->command->info("  Previous: {$existingShares} shares @ Rp " . number_format($existingAvgPrice));
                $this->command->info("  Added: {$newQuantity} shares @ Rp " . number_format($newPricePerShare));
                $this->command->info("  New Total: {$totalShares} shares (" . $totalLots . " lots) @ Rp " . number_format($newAveragePrice));
                $this->command->info("  Total Value: Rp " . number_format($totalShares * $newAveragePrice));
            }
        }
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
