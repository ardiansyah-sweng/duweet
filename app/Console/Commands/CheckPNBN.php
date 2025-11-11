<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckPNBN extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:pnbn';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check PNBN asset calculation details';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== PNBN Asset Analysis ===');
        
        // 1. Check Financial Accounts
        $this->info('1. Financial Accounts PNBN:');
        $pnbnAccounts = DB::table('financial_accounts')
            ->where('name', 'LIKE', '%PNBN%')
            ->select('id', 'name', 'balance', 'initial_balance', 'stock_symbol')
            ->get();
            
        foreach($pnbnAccounts as $acc) {
            $this->line("ID {$acc->id}: {$acc->name}");
            $this->line("  Initial: Rp " . number_format($acc->initial_balance));
            $this->line("  Current: Rp " . number_format($acc->balance));
            $this->line("  Symbol: " . ($acc->stock_symbol ?? 'NULL'));
            $this->newLine();
        }
        
        // 2. Check Assets
        $this->info('2. Assets Table PNBN:');
        $pnbnAssets = DB::table('assets as a')
            ->join('financial_accounts as fa', 'a.financial_account_id', '=', 'fa.id')
            ->where('fa.name', 'LIKE', '%PNBN%')
            ->select('a.*', 'fa.name as account_name')
            ->get();
            
        foreach($pnbnAssets as $asset) {
            $value = $asset->buy_quantity * $asset->bought_price;
            $this->line("Asset ID {$asset->id} for Account: {$asset->account_name}");
            $this->line("  Quantity: {$asset->buy_quantity} lots");
            $this->line("  Price: Rp " . number_format($asset->bought_price) . " per lot");
            $this->line("  Total Value: Rp " . number_format($value));
            $this->line("  Measurement: {$asset->measurement_unit}");
            $this->newLine();
        }
        
        // 3. Expected Calculation
        $this->info('3. Expected PNBN Calculation (from AssetSeeder):');
        $this->line("Data dari AssetSeeder.php:");
        $this->line("  BOUGHT_PRICE: 1678 (per lot)");
        $this->line("  BUY_QTY: 35 (lots)");
        $this->line("  MEASUREMENT: 'lot'");
        $this->newLine();
        
        $baseCost = 1678 * 35;
        $unitValue = 100; // lot unit value
        $expectedBalance = $baseCost * $unitValue;
        
        $this->line("Expected Calculation:");
        $this->line("  Base Cost: 1678 × 35 = Rp " . number_format($baseCost));
        $this->line("  Unit Value (lot): 100");
        $this->line("  Expected Balance: {$baseCost} × 100 = Rp " . number_format($expectedBalance));
        
        // 4. Stock Price Check
        $this->info('4. Current Stock Price:');
        try {
            $yahooService = new \App\Services\YahooFinanceService();
            $priceData = $yahooService->getStockPrice('PNBN');
            
            if ($priceData && $priceData['price'] > 0) {
                $currentPrice = $priceData['price'];
                $shares = 35 * 100; // 35 lots × 100 shares
                $currentMarketValue = $shares * $currentPrice;
                
                $this->line("Current PNBN Price: Rp " . number_format($currentPrice) . " per share");
                $this->line("Total Shares: 35 lots × 100 = 3,500 shares");
                $this->line("Current Market Value: 3,500 × {$currentPrice} = Rp " . number_format($currentMarketValue));
                
                $costBasis = $expectedBalance;
                $gainLoss = $currentMarketValue - $costBasis;
                $gainLossPercent = ($gainLoss / $costBasis) * 100;
                
                $status = $gainLoss >= 0 ? '📈 Profit' : '📉 Loss';
                $this->line("Performance: {$status} Rp " . number_format($gainLoss) . " (" . number_format($gainLossPercent, 2) . "%)");
            } else {
                $this->warn("Failed to get current PNBN price");
            }
        } catch (\Exception $e) {
            $this->error("Error getting PNBN price: " . $e->getMessage());
        }
        
        return 0;
    }
}
