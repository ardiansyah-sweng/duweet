<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FinancialAccount;
use Illuminate\Support\Facades\DB;

class CheckStockPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:stock-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check current stock prices and verify calculations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking Stock Prices & Real-time Values...');
        $this->line('');

        // Get all stock accounts
        $stockAccounts = FinancialAccount::whereNotNull('stock_symbol')->get();
        
        $this->info('📈 Current Stock Prices in Database:');
        foreach ($stockAccounts as $account) {
            $this->line(sprintf(
                '   %s (%s): Rp %s - Updated: %s',
                $account->account_name,
                $account->stock_symbol,
                number_format($account->current_price, 0, ',', '.'),
                $account->price_updated_at ? $account->price_updated_at->format('Y-m-d H:i:s') : 'Never'
            ));
        }

        $this->line('');
        $this->info('🧮 Verifying Real-time Calculations:');

        // Check sample stocks with assets data
        $samples = ['PNBN', 'BBRI', 'KEEN'];
        
        foreach ($samples as $symbol) {
            $account = FinancialAccount::where('stock_symbol', $symbol)->first();
            
            // Get asset data from assets table
            $asset = DB::table('assets')
                ->where('financial_account_id', $account->id ?? 0)
                ->where('is_sold', false)
                ->first();

            if ($account && $asset) {
                $this->line('');
                $this->warn("📊 {$symbol} Real-time Analysis:");
                
                // Asset data (using buy_quantity and bought_price)
                $quantity = $asset->buy_quantity;
                $boughtPrice = $asset->bought_price;
                $totalShares = $quantity * 100; // 1 lot = 100 shares for Indonesian stocks
                
                $this->line("   Asset: {$quantity} lots @ Rp {$boughtPrice} = Base Rp " . number_format($quantity * $boughtPrice));
                $this->line("   Shares: {$quantity} lots × 100 = {$totalShares} shares");
                
                // Current real-time price calculation
                $currentPrice = $account->current_price;
                $currentValue = $totalShares * $currentPrice;
                
                $this->line("   Current Price (Real-time): Rp {$currentPrice}/share");
                $this->line("   Current Market Value: {$totalShares} × {$currentPrice} = Rp " . number_format($currentValue));
                $this->line("   Account Balance (DB): Rp " . number_format($account->current_balance));
                
                // Performance calculation
                $initialValue = ($quantity * $boughtPrice) * 100;
                $performance = (($currentValue - $initialValue) / $initialValue) * 100;
                
                $this->line("   Initial Investment: Rp " . number_format($initialValue));
                $this->line("   Performance: " . number_format($performance, 2) . "%");
                
                // Verify if account balance matches real-time calculation
                if (abs($currentValue - $account->current_balance) < 1) {
                    $this->info("   ✅ Real-time calculation CORRECT!");
                } else {
                    $this->error("   ❌ Mismatch! Expected: Rp " . number_format($currentValue) . " Got: Rp " . number_format($account->current_balance));
                }
            } else {
                $this->line("   ⚠️  No data found for {$symbol}");
            }
        }

        $this->line('');
        $this->info('🎯 Summary:');
        $lastUpdate = $stockAccounts->max('price_updated_at');
        $this->line("   Last price update: " . ($lastUpdate ? $lastUpdate->format('Y-m-d H:i:s') : 'Never'));
        $this->line("   Total stocks tracked: " . $stockAccounts->count());
        
        $upToDate = $lastUpdate && $lastUpdate->diffInHours(now()) < 24;
        if ($upToDate) {
            $this->info('   ✅ Stock prices are up-to-date (within 24 hours)');
        } else {
            $this->warn('   ⚠️  Stock prices may be outdated. Run: php artisan stock:refresh');
        }
    }
}
