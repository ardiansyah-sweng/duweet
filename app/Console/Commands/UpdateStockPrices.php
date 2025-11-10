<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\YahooFinanceService;

class UpdateStockPrices extends Command
{
    protected $signature = 'stocks:update 
                           {--symbol=* : Specific symbols to update}
                           {--test : Test connection only}
                           {--market-status : Check market status}';
                           
    protected $description = 'Update stock prices from Yahoo Finance API';

    protected YahooFinanceService $yahooService;

    public function __construct(YahooFinanceService $yahooService)
    {
        parent::__construct();
        $this->yahooService = $yahooService;
    }

    public function handle(): int
    {
        $this->info("🚀 Yahoo Finance Stock Price Updater");
        $this->newLine();

        // Test connection option
        if ($this->option('test')) {
            return $this->testConnection();
        }

        // Market status option
        if ($this->option('market-status')) {
            return $this->showMarketStatus();
        }

        // Main update process
        return $this->updateStockPrices();
    }

    /**
     * Test Yahoo Finance connection
     */
    private function testConnection(): int
    {
        $this->info("🔌 Testing Yahoo Finance connection...");
        
        $testResult = $this->yahooService->testConnection();
        
        if ($testResult['success']) {
            $this->info("✅ Connection successful!");
            $this->line("   Response time: {$testResult['response_time_ms']}ms");
            $this->line("   Test symbol: {$testResult['test_symbol']}");
            
            if ($testResult['test_data']) {
                $data = $testResult['test_data'];
                $this->line("   Price: {$data['price']} {$data['currency']}");
                $this->line("   Change: {$data['change']} ({$data['change_percent']}%)");
                $this->line("   Market state: {$data['market_state']}");
            }
        } else {
            $this->error("❌ Connection failed!");
            return 1;
        }

        return 0;
    }

    /**
     * Show market status
     */
    private function showMarketStatus(): int
    {
        $this->info("📊 IDX Market Status");
        
        $status = $this->yahooService->getMarketStatus();
        
        $statusIcon = $status['is_open'] ? '🟢' : '🔴';
        $statusText = $status['is_open'] ? 'OPEN' : 'CLOSED';
        
        $this->line("   {$statusIcon} Market Status: {$statusText}");
        $this->line("   🕐 Current Time: {$status['current_time']}");
        $this->line("   🏢 Market Hours: 09:00 - 16:00 WIB");
        
        if (!$status['is_open']) {
            $this->line("   ⏰ Next Open: {$status['next_open']}");
        }

        return 0;
    }

    /**
     * Update stock prices
     */
    private function updateStockPrices(): int
    {
        $symbols = $this->option('symbol');
        
        if (!empty($symbols)) {
            return $this->updateSpecificSymbols($symbols);
        } else {
            return $this->updateAllStockAssets();
        }
    }

    /**
     * Update specific symbols
     */
    private function updateSpecificSymbols(array $symbols): int
    {
        $this->info("📈 Updating specific symbols: " . implode(', ', $symbols));
        $this->newLine();

        $results = $this->yahooService->getMultipleStockPrices($symbols);
        
        foreach ($results as $symbol => $data) {
            if ($data) {
                $changeIcon = $data['change'] >= 0 ? '📈' : '📉';
                $this->line("   {$changeIcon} {$symbol}: {$data['price']} IDR " . 
                           "(Change: {$data['change']}, {$data['change_percent']}%)");
            } else {
                $this->warn("   ⚠️  Failed to fetch price for {$symbol}");
            }
        }

        return 0;
    }

    /**
     * Update all stock assets in database
     */
    private function updateAllStockAssets(): int
    {
        $this->info("🔄 Updating all stock asset prices...");
        
        // Check market status first
        $marketStatus = $this->yahooService->getMarketStatus();
        
        if (!$marketStatus['is_open']) {
            $this->warn("⚠️  Market is currently closed. Prices may not be real-time.");
            
            if (!$this->confirm('Continue with update?', true)) {
                $this->info("Update cancelled.");
                return 0;
            }
        }

        $progressBar = $this->output->createProgressBar(1);
        $progressBar->start();

        $updatedAssets = $this->yahooService->updateAssetStockPrices();

        $progressBar->finish();
        $this->newLine(2);

        if (empty($updatedAssets)) {
            $this->warn("⚠️  No stock assets found or updated.");
            return 0;
        }

        $this->info("✅ Updated " . count($updatedAssets) . " stock assets:");
        $this->newLine();

        // Create a table for better display
        $headers = ['Symbol', 'Account', 'Price', 'Change', 'Change %', 'Volume', 'Status'];
        $rows = [];

        foreach ($updatedAssets as $asset) {
            $changeIcon = $asset['change'] >= 0 ? '📈' : '📉';
            $marketIcon = $asset['market_state'] === 'REGULAR' ? '🟢' : '🟡';
            
            $rows[] = [
                $asset['symbol'],
                $asset['account_name'],
                number_format($asset['price'], 0),
                number_format($asset['change'], 2),
                number_format($asset['change_percent'], 2) . '%',
                number_format($asset['volume']),
                $marketIcon . ' ' . $asset['market_state']
            ];
        }

        $this->table($headers, $rows);

        // Summary
        $totalGainers = count(array_filter($updatedAssets, fn($a) => $a['change'] > 0));
        $totalLosers = count(array_filter($updatedAssets, fn($a) => $a['change'] < 0));
        $totalFlat = count($updatedAssets) - $totalGainers - $totalLosers;

        $this->newLine();
        $this->info("📊 Summary:");
        $this->line("   📈 Gainers: {$totalGainers}");
        $this->line("   📉 Losers: {$totalLosers}");
        $this->line("   ➡️  Unchanged: {$totalFlat}");

        $this->newLine();
        $this->info("🎯 Stock price update completed!");

        return 0;
    }
}