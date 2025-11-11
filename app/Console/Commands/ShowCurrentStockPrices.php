<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FinancialAccount;
use App\Services\YahooFinanceService;
use Illuminate\Support\Facades\DB;

class ShowCurrentStockPrices extends Command
{
    protected $signature = 'stock:prices {--refresh : Refresh prices from Yahoo Finance before showing}';
    protected $description = 'Display current stock prices for all holdings';

    public function handle()
    {
        $this->info('📈 Current Stock Prices Overview');
        $this->line('');

        // Refresh prices if requested
        if ($this->option('refresh')) {
            $this->info('🔄 Refreshing prices from Yahoo Finance...');
            $this->call('stocks:refresh');
            $this->line('');
        }

        // Get all stock accounts
        $stockAccounts = FinancialAccount::whereNotNull('stock_symbol')
            ->where('stock_symbol', '!=', '')
            ->orderBy('name')
            ->get(['id', 'name', 'stock_symbol', 'current_price', 'price_updated_at']);

        if ($stockAccounts->isEmpty()) {
            $this->warn('No stock accounts found!');
            return 1;
        }

        // Display header
        $headers = ['Stock', 'Symbol', 'Current Price', 'Last Updated', 'Holdings', 'Market Value', 'P&L %'];
        $rows = [];

        foreach ($stockAccounts as $account) {
            // Get asset data
            $asset = DB::table('assets')
                ->where('financial_account_id', $account->id)
                ->where('is_sold', false)
                ->first();

            $holdings = $asset ? $asset->buy_quantity . ' lots' : 'N/A';
            $totalShares = $asset ? $asset->buy_quantity * 100 : 0;
            $currentValue = $totalShares * ($account->current_price ?? 0);
            $initialValue = $asset ? ($asset->buy_quantity * $asset->bought_price * 100) : 0;
            
            // Calculate P&L percentage
            $pnlPercent = 0;
            if ($initialValue > 0) {
                $pnlPercent = (($currentValue - $initialValue) / $initialValue) * 100;
            }

            $pnlDisplay = $pnlPercent > 0 ? 
                '<fg=green>+' . number_format($pnlPercent, 2) . '%</>' : 
                '<fg=red>' . number_format($pnlPercent, 2) . '%</>';

            // Format update time properly
            $updateTime = 'Never';
            if ($account->price_updated_at) {
                $updateTime = is_string($account->price_updated_at) 
                    ? $account->price_updated_at 
                    : $account->price_updated_at->format('Y-m-d H:i');
            }

            $rows[] = [
                $account->name,
                $account->stock_symbol,
                'Rp ' . number_format($account->current_price ?? 0),
                $updateTime,
                $holdings,
                'Rp ' . number_format($currentValue),
                $pnlDisplay
            ];
        }

        $this->table($headers, $rows);

        // Summary
        $totalMarketValue = $stockAccounts->sum(function($account) {
            $asset = DB::table('assets')
                ->where('financial_account_id', $account->id)
                ->where('is_sold', false)
                ->first();
            $totalShares = $asset ? $asset->buy_quantity * 100 : 0;
            return $totalShares * ($account->current_price ?? 0);
        });

        $this->line('');
        $this->info('📊 Portfolio Summary:');
        $this->line("   Total Stocks: " . $stockAccounts->count());
        $this->line("   Total Market Value: Rp " . number_format($totalMarketValue));
        
        $lastUpdate = $stockAccounts->max('price_updated_at');
        if ($lastUpdate) {
            // Handle string date format
            if (is_string($lastUpdate)) {
                $lastUpdateCarbon = \Carbon\Carbon::parse($lastUpdate);
                $hoursAgo = $lastUpdateCarbon->diffInHours(now());
                $displayDate = $lastUpdate;
            } else {
                $hoursAgo = $lastUpdate->diffInHours(now());
                $displayDate = $lastUpdate->format('Y-m-d H:i:s');
            }
            
            $updateStatus = $hoursAgo < 1 ? 'Very Recent' : 
                           ($hoursAgo < 24 ? 'Recent' : 'Outdated');
            $this->line("   Last Update: " . $displayDate . " ({$updateStatus})");
        }

        $this->line('');
        $this->comment('💡 Tips:');
        $this->comment('   • Run with --refresh to get latest prices');
        $this->comment('   • Prices auto-refresh daily at 9:30 AM and 4:00 PM');
        $this->comment('   • Use "php artisan stocks:refresh" for manual refresh');

        return 0;
    }
}
