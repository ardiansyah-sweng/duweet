<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\YahooFinanceService;

class RefreshStockValues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stocks:refresh {--symbol=* : Specific stock symbols to update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh stock values from Yahoo Finance and update financial account balances';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Refreshing Stock Values from Yahoo Finance...');
        $this->newLine();

        $specificSymbols = $this->option('symbol');
        $yahooService = new YahooFinanceService();
        
        // Get all stock assets with their symbols
        $stockAssets = DB::table('assets as a')
            ->join('financial_accounts as fa', 'a.financial_account_id', '=', 'fa.id')
            ->where('a.is_sold', false)
            ->whereNotNull('fa.stock_symbol')
            ->where('fa.stock_symbol', '!=', '')
            ->select([
                'a.id as asset_id',
                'a.financial_account_id',
                'a.buy_quantity',
                'a.bought_price',
                'fa.name as account_name',
                'fa.stock_symbol',
                'fa.balance as current_balance'
            ])
            ->get();

        if ($stockAssets->isEmpty()) {
            $this->warn('No stock assets found to update.');
            return 0;
        }

        $updated = 0;
        $failed = 0;

        foreach ($stockAssets as $asset) {
            // Skip if specific symbols requested and this isn't one of them
            if (!empty($specificSymbols) && !in_array($asset->stock_symbol, $specificSymbols)) {
                continue;
            }

            try {
                $this->line("Updating {$asset->account_name} ({$asset->stock_symbol})...");
                
                // Get current price from Yahoo Finance
                $priceData = $yahooService->getStockPrice($asset->stock_symbol);
                
                if ($priceData && $priceData['price'] > 0) {
                    $currentPrice = $priceData['price'];
                    
                    // Calculate current market value
                    // asset.buy_quantity is in lots, need to convert to shares
                    $shares = $asset->buy_quantity * 100;
                    $marketValue = $shares * $currentPrice;
                    
                    // Update financial account balance with current market value
                    DB::table('financial_accounts')
                        ->where('id', $asset->financial_account_id)
                        ->update([
                            'balance' => $marketValue,
                            'current_price' => $currentPrice,
                            'updated_at' => now(),
                        ]);

                    // Calculate performance
                    $costBasis = $shares * $asset->bought_price;
                    $gainLoss = $marketValue - $costBasis;
                    $gainLossPercent = $costBasis > 0 ? (($gainLoss / $costBasis) * 100) : 0;
                    
                    $status = $gainLoss >= 0 ? '📈' : '📉';
                    $changeIcon = $priceData['change'] >= 0 ? '📈' : '📉';
                    
                    $this->info("   ✅ {$asset->account_name}:");
                    $this->info("      💰 Market Value: Rp " . number_format($marketValue));
                    $this->info("      📊 Current Price: Rp " . number_format($currentPrice) . " per share");
                    $this->info("      {$changeIcon} Daily Change: " . number_format($priceData['change'], 2) . " (" . number_format($priceData['change_percent'], 2) . "%)");
                    $this->info("      {$status} Total P&L: Rp " . number_format($gainLoss) . " (" . number_format($gainLossPercent, 2) . "%)");
                    $this->info("      📅 Source: {$priceData['source']}");
                    
                    $updated++;
                } else {
                    $this->error("   ❌ Failed to get price for {$asset->stock_symbol}");
                    $failed++;
                }
                
            } catch (\Exception $e) {
                $this->error("   ❌ Error updating {$asset->account_name}: " . $e->getMessage());
                $failed++;
            }
            
            $this->newLine();
        }

        $this->info("🎯 Stock Update Summary:");
        $this->info("   ✅ Successfully Updated: {$updated} stocks");
        if ($failed > 0) {
            $this->warn("   ❌ Failed to Update: {$failed} stocks");
        }
        $this->info("   🕐 Last Updated: " . now()->format('Y-m-d H:i:s'));

        return 0;
    }
}
