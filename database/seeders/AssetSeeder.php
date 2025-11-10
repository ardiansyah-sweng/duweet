<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Constants\AssetColumns as Cols;
use App\Enums\Measurement;
use App\Services\YahooFinanceService;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assets = [
            // 1. Kas dan bank - BCA Ayah
            [
                Cols::ACCOUNT_ID => 10,
                Cols::ACQUISITION_DATE => '2023-01-15',
                Cols::IS_LIQUID => true,
                Cols::IS_PRODUCTIVE => false,
                Cols::MEASUREMENT => 'unit',
                'default_balance' => 210798336,
                'current_market_price' => 210798336, // Same for cash
            ],

            // 2. Kas dan bank - BPD Syariah Istri
            [
                Cols::ACCOUNT_ID => 13,
                Cols::ACQUISITION_DATE => '2023-01-15',
                Cols::IS_LIQUID => true,
                Cols::IS_PRODUCTIVE => false,
                Cols::MEASUREMENT => 'unit',
                'default_balance' => 101697979,
                'current_market_price' => 101697979, // Same for cash
            ],

            // 3. Kas dan bank - Danamon Istri
            [
                Cols::ACCOUNT_ID => 14,
                Cols::ACQUISITION_DATE => '2023-01-15',
                Cols::IS_LIQUID => true,
                Cols::IS_PRODUCTIVE => false,
                Cols::MEASUREMENT => 'unit',
                'default_balance' => 11096953,
                'current_market_price' => 11096953, // Same for cash
            ],

            // 2. Kas dan bank - BSI Istri
            [
                Cols::ACCOUNT_ID => 15,
                Cols::ACQUISITION_DATE => '2023-01-15',
                Cols::IS_LIQUID => true,
                Cols::IS_PRODUCTIVE => false,
                Cols::MEASUREMENT => 'unit',
                'default_balance' => 37376791,
                'current_market_price' => 37376791, // Same for cash
            ],

            // 3. RDN Mirae
            [
                Cols::ACCOUNT_ID => 17,
                Cols::ACQUISITION_DATE => '2023-01-15',
                Cols::IS_LIQUID => true,
                Cols::IS_PRODUCTIVE => false,
                Cols::MEASUREMENT => 'unit',
                'default_balance' => 54521423,
                'current_market_price' => 54521423, // Same for cash
            ],


            // 4. Saham KEEN - High growth stock
            [
                Cols::ACCOUNT_ID => 18,
                Cols::ACQUISITION_DATE => '2023-05-16',
                Cols::BOUGHT_PRICE => 918,      // Historical buy price per lot
                Cols::BUY_QTY => 1792,          // 1792 lots
                'stock_symbol' => 'KEEN',       // For Yahoo Finance API
                'current_market_price' => 1150, // Fallback if API fails
                Cols::IS_LIQUID => true,
                Cols::IS_PRODUCTIVE => true,
                Cols::MEASUREMENT => 'lot',
            ],

            // 4. Saham PTPS - Moderate growth stock
            [
                Cols::ACCOUNT_ID => 19,
                Cols::ACQUISITION_DATE => '2025-10-22',
                Cols::BOUGHT_PRICE => 211,     // Historical buy price per lot
                Cols::BUY_QTY => 1520,         // 1520 lots
                'stock_symbol' => 'PTPS',      // For Yahoo Finance API
                'current_market_price' => 245, // Fallback if API fails
                Cols::IS_LIQUID => true,
                Cols::IS_PRODUCTIVE => true,
                Cols::MEASUREMENT => 'lot',
            ],

            // 5. Saham BBRI - Moderate growth stock
            [
                Cols::ACCOUNT_ID => 20,
                Cols::ACQUISITION_DATE => '2025-11-07',
                Cols::BOUGHT_PRICE => 3985,     // Historical buy price per lot
                Cols::BUY_QTY => 26,         // 26 lots
                'stock_symbol' => 'BBRI',      // For Yahoo Finance API
                'current_market_price' => 3985, // Fallback if API fails
                Cols::IS_LIQUID => true,
                Cols::IS_PRODUCTIVE => true,
                Cols::MEASUREMENT => 'lot',
            ],

            // 4. Saham ESSA - Stock
            [
                Cols::ACCOUNT_ID => 21,
                Cols::ACQUISITION_DATE => '2025-11-07',
                Cols::BOUGHT_PRICE => 650,     // Initial buy
                Cols::BUY_QTY => 156,
                'stock_symbol' => 'ESSA',      // For Yahoo Finance API
                'current_market_price' => 650, // Fallback if API fails
                Cols::IS_LIQUID => true,
                Cols::IS_PRODUCTIVE => true,
                Cols::MEASUREMENT => 'lot',
            ],

            // 5. Saham MPMX - Stock
            [
                Cols::ACCOUNT_ID => 22,
                Cols::ACQUISITION_DATE => '2025-06-07',
                Cols::BOUGHT_PRICE => 1100,     // Initial buy
                Cols::BUY_QTY => 140,
                'stock_symbol' => 'MPMX',      // For Yahoo Finance API
                'current_market_price' => 1100, // Fallback if API fails
                Cols::IS_LIQUID => true,
                Cols::IS_PRODUCTIVE => true,
                Cols::MEASUREMENT => 'lot',
            ],

            // 6. Saham RALS - Stock
            [
                Cols::ACCOUNT_ID => 23,
                Cols::ACQUISITION_DATE => '2025-06-07',
                Cols::BOUGHT_PRICE => 418,     // Initial buy
                Cols::BUY_QTY => 360,
                'stock_symbol' => 'RALS',      // For Yahoo Finance API
                'current_market_price' => 418, // Fallback if API fails
                Cols::IS_LIQUID => true,
                Cols::IS_PRODUCTIVE => true,
                Cols::MEASUREMENT => 'lot',
            ],

            // 7. Saham BEST - Stock
            [
                Cols::ACCOUNT_ID => 24,
                Cols::ACQUISITION_DATE => '2023-06-07',
                Cols::BOUGHT_PRICE => 115,     // Initial buy
                Cols::BUY_QTY => 2175,
                'stock_symbol' => 'BEST',      // For Yahoo Finance API
                'current_market_price' => 115, // Fallback if API fails
                Cols::IS_LIQUID => true,
                Cols::IS_PRODUCTIVE => true,
                Cols::MEASUREMENT => 'lot',
            ],

            // 8. Saham PNBN - Stock
            [
                Cols::ACCOUNT_ID => 25,
                Cols::ACQUISITION_DATE => '2025-06-07',
                Cols::BOUGHT_PRICE => 1678,     // Initial buy
                Cols::BUY_QTY => 35,
                'stock_symbol' => 'PNBN',      // For Yahoo Finance API
                'current_market_price' => 1678, // Fallback if API fails
                Cols::IS_LIQUID => true,
                Cols::IS_PRODUCTIVE => true,
                Cols::MEASUREMENT => 'lot',
            ],

            // 9. Saham PWON - Stock
            [
                Cols::ACCOUNT_ID => 26,
                Cols::ACQUISITION_DATE => '2023-06-07',
                Cols::BOUGHT_PRICE => 484,     // Initial buy
                Cols::BUY_QTY => 579,
                'stock_symbol' => 'PWON',      // For Yahoo Finance API
                'current_market_price' => 484, // Fallback if API fails
                Cols::IS_LIQUID => true,
                Cols::IS_PRODUCTIVE => true,
                Cols::MEASUREMENT => 'lot',
            ],

            // 10. RDPU Sucorindo - Mutual Fund
            [
                Cols::ACCOUNT_ID => 27,
                Cols::ACQUISITION_DATE => '2025-02-02',
                Cols::BOUGHT_PRICE => 200000000,     // Initial buy
                Cols::BUY_QTY => 1,
                'current_market_price' => 207296570, // Fallback if API fails
                Cols::IS_LIQUID => true,
                Cols::IS_PRODUCTIVE => true,
                Cols::MEASUREMENT => 'unit',
            ],

            // 11. Emas Batangan - Store of value
            [
                Cols::ACCOUNT_ID => 28,
                Cols::ACQUISITION_DATE => '2022-03-10',
                Cols::BOUGHT_PRICE => 473333,    // Buy price per gram
                Cols::BUY_QTY => 3,           // 3 gram
                'current_market_price' => 2260000, // Current
                Cols::IS_LIQUID => true,
                Cols::IS_PRODUCTIVE => true,
                Cols::MEASUREMENT => 'gram',
            ],

            // 12. Emas Cincin Borobudur - Store of value
            [
                Cols::ACCOUNT_ID => 29,
                Cols::ACQUISITION_DATE => '2022-05-07',
                Cols::BOUGHT_PRICE => 835820,    // Buy price per gram
                Cols::BUY_QTY => 6.7,           // 3 gram
                'current_market_price' => 1582000, // Current
                Cols::IS_LIQUID => true,
                Cols::IS_PRODUCTIVE => true,
                Cols::MEASUREMENT => 'gram',
            ],

            // 13. Emas Gelang - Store of value
            [
                Cols::ACCOUNT_ID => 30,
                Cols::ACQUISITION_DATE => '2023-04-25',
                Cols::BOUGHT_PRICE => 888059,    // Buy price per gram
                Cols::BUY_QTY => 6.7,           // 3 gram
                'current_market_price' => 1582000, // Current
                Cols::IS_LIQUID => true,
                Cols::IS_PRODUCTIVE => true,
                Cols::MEASUREMENT => 'gram',
            ],

            // 12. Rumah Tinggal - Property appreciation
            [
                Cols::ACCOUNT_ID => 31,
                Cols::ACQUISITION_DATE => '2009-06-15',
                Cols::BOUGHT_PRICE => 337500000,  // Purchase price
                Cols::BUY_QTY => 1,               // 1 house
                'current_market_price' => 400000000, // Current appraised value (+30%)
                Cols::IS_LIQUID => false,
                Cols::IS_PRODUCTIVE => false,
                Cols::MEASUREMENT => 'unit',
            ],

            // 13. Mobil Brio - Depreciating asset
            [
                Cols::ACCOUNT_ID => 33,
                Cols::ACQUISITION_DATE => '2019-02-20',
                Cols::BOUGHT_PRICE => 159000000,  // Purchase price
                Cols::BUY_QTY => 1,               // 1 car
                'current_market_price' => 130000000, // Depreciated value (-20%)
                Cols::IS_LIQUID => false,
                Cols::IS_PRODUCTIVE => false,
                Cols::MEASUREMENT => 'unit',
            ],

        ];

        // Insert assets with proper data validation
        $this->insertAssetsWithValidation($assets);
    }

    /**
     * Insert assets with validation and error handling
     */
    private function insertAssetsWithValidation(array $assets): void
    {
        $insertedCount = 0;
        $skippedCount = 0;

        foreach ($assets as $index => $assetData) {
            try {
                // Check if financial_account_id exists (in real scenario)
                // For seeding purpose, we'll use the index + 1 as account_id
                // $assetData[Cols::ACCOUNT_ID] = $index + 1;

                // Insert asset
                $assetId = DB::table(config('db_tables.asset', 'assets'))->insertGetId([
                    Cols::ACCOUNT_ID => $assetData[Cols::ACCOUNT_ID],
                    Cols::ACQUISITION_DATE => $assetData[Cols::ACQUISITION_DATE],
                    Cols::SOLD_DATE => $assetData[Cols::SOLD_DATE] ?? null,
                    Cols::BOUGHT_PRICE => $assetData[Cols::BOUGHT_PRICE] ?? 0,
                    Cols::SOLD_PRICE => $assetData[Cols::SOLD_PRICE] ?? 0,
                    Cols::BUY_QTY => $assetData[Cols::BUY_QTY] ?? 0,
                    Cols::SELL_QTY => $assetData[Cols::SELL_QTY] ?? 0,
                    Cols::IS_LIQUID => $assetData[Cols::IS_LIQUID],
                    Cols::IS_PRODUCTIVE => $assetData[Cols::IS_PRODUCTIVE],
                    Cols::MEASUREMENT => $assetData[Cols::MEASUREMENT],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Calculate and update FinancialAccount balance
                $this->updateFinancialAccountBalance($assetData);

                $insertedCount++;
                $this->command->info("✅ Created asset ID: {$assetId} - Account: {$assetData[Cols::ACCOUNT_ID]} - {$assetData[Cols::MEASUREMENT]}");

            } catch (\Exception $e) {
                $skippedCount++;
                $this->command->warn("⚠️  Skipped asset for account {$assetData[Cols::ACCOUNT_ID]}: {$e->getMessage()}");
            }
        }

        $this->command->info("🎯 Asset seeding completed:");
        $this->command->info("   ✅ Inserted: {$insertedCount} assets");
        if ($skippedCount > 0) {
            $this->command->info("   ⚠️  Skipped: {$skippedCount} assets");
        }
    }

    /**
     * Update FinancialAccount balance based on asset data with current market value
     */
    private function updateFinancialAccountBalance(array $assetData): void
    {
        $initialBalance = 0;
        $currentBalance = 0;
        
        if (isset($assetData[Cols::BOUGHT_PRICE]) && isset($assetData[Cols::BUY_QTY])) {
            // Get measurement unit value
            $measurementUnit = $assetData[Cols::MEASUREMENT] ?? 'unit';
            $measurement = Measurement::fromValue($measurementUnit);
            $unitValue = $measurement ? $measurement->getUnitValue() : 1;
            
            // Calculate INITIAL BALANCE (historical cost basis)
            $baseCost = $assetData[Cols::BOUGHT_PRICE] * $assetData[Cols::BUY_QTY];
            $initialBalance = $baseCost * $unitValue;
            
        // Get CURRENT MARKET PRICE from Yahoo Finance API (for stocks)
        $currentMarketPrice = $assetData['current_market_price']; // fallback
        
        if (isset($assetData['stock_symbol'])) {
            try {
                $yahooService = new YahooFinanceService();
                $priceData = $yahooService->getStockPrice($assetData['stock_symbol']);
                
                if ($priceData && $priceData['price'] > 0) {
                    $currentMarketPrice = $priceData['price'];
                    $this->command->info("   📡 Live price for {$assetData['stock_symbol']}: " . number_format($currentMarketPrice) . " (from {$priceData['source']})");
                    
                    if ($priceData['change'] != 0) {
                        $changeIcon = $priceData['change'] >= 0 ? '📈' : '📉';
                        $this->command->info("   {$changeIcon} Daily change: " . number_format($priceData['change'], 2) . " (" . number_format($priceData['change_percent'], 2) . "%)");
                    }
                } else {
                    $this->command->warn("   ⚠️  Failed to fetch live price for {$assetData['stock_symbol']}, using fallback: " . number_format($currentMarketPrice));
                }
            } catch (\Exception $e) {
                $this->command->warn("   ⚠️  Yahoo Finance API error for {$assetData['stock_symbol']}: {$e->getMessage()}");
                $this->command->info("   Using fallback price: " . number_format($currentMarketPrice));
            }
        }
        
        // Calculate CURRENT BALANCE (market value)
        $currentMarketValue = $currentMarketPrice * $assetData[Cols::BUY_QTY];
        $currentBalance = $currentMarketValue * $unitValue;
            
            // Calculate performance metrics
            $gainLoss = $currentBalance - $initialBalance;
            $gainLossPercent = $initialBalance > 0 ? (($gainLoss / $initialBalance) * 100) : 0;
            $status = $gainLoss >= 0 ? '📈' : '📉';
            
            $this->command->info("   📊 Asset Performance Analysis:");
            $this->command->info("      Historical Cost: {$assetData[Cols::BOUGHT_PRICE]} × {$assetData[Cols::BUY_QTY]} × {$unitValue} = " . number_format($initialBalance));
            $this->command->info("      Current Value:   " . number_format($currentMarketPrice) . " × {$assetData[Cols::BUY_QTY]} × {$unitValue} = " . number_format($currentBalance));
            $this->command->info("      {$status} P&L:          " . number_format($gainLoss) . " (" . number_format($gainLossPercent, 2) . "%)");
            
        } else {
            // For cash or assets without price data
            $initialBalance = $currentBalance = $assetData['default_balance'] ?? 0;
            $this->command->info("   💵 Cash Asset: " . number_format($currentBalance));
        }

        // Update the corresponding FinancialAccount with both values
        if ($initialBalance > 0 || $currentBalance > 0) {
            DB::table(config('db_tables.financial_account', 'financial_accounts'))
                ->where('id', $assetData[Cols::ACCOUNT_ID])
                ->update([
                    'initial_balance' => $initialBalance,  // Historical cost basis
                    'balance' => $currentBalance,          // Current market value
                    'updated_at' => now(),
                ]);

            $this->command->info("   ✅ Updated FinancialAccount ID {$assetData[Cols::ACCOUNT_ID]}:");
            $this->command->info("      Initial Balance: " . number_format($initialBalance));
            $this->command->info("      Current Balance: " . number_format($currentBalance));
        }
    }

}