<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class YahooFinanceService
{
    private const API_TIMEOUT = 30; // seconds
    private const CACHE_DURATION = 300; // 5 minutes cache

    /**
     * Get stock price from Yahoo Finance API
     */
    public function getStockPrice(string $symbol): ?array
    {
        $cacheKey = "yahoo_stock_price_{$symbol}";
        
        // Try to get from cache first
        if (Cache::has($cacheKey)) {
            Log::info("Stock price fetched from cache for {$symbol}");
            return Cache::get($cacheKey);
        }

        try {
            $yahooSymbol = $this->convertToYahooSymbol($symbol);
            
            $response = Http::timeout(self::API_TIMEOUT)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                ])
                ->get("https://query1.finance.yahoo.com/v8/finance/chart/{$yahooSymbol}");

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['chart']['result'][0]['meta']['regularMarketPrice'])) {
                    $meta = $data['chart']['result'][0]['meta'];
                    
                    $result = [
                        'symbol' => $symbol,
                        'yahoo_symbol' => $yahooSymbol,
                        'price' => $meta['regularMarketPrice'],
                        'currency' => $meta['currency'] ?? 'IDR',
                        'source' => 'yahoo_finance',
                        'timestamp' => now(),
                        'previous_close' => $meta['previousClose'] ?? 0,
                        'change' => $meta['regularMarketChange'] ?? 0,
                        'change_percent' => $meta['regularMarketChangePercent'] ?? 0,
                        'volume' => $meta['regularMarketVolume'] ?? 0,
                        'market_state' => $meta['marketState'] ?? 'UNKNOWN',
                    ];
                    
                    // Cache the result
                    Cache::put($cacheKey, $result, self::CACHE_DURATION);
                    
                    Log::info("Stock price fetched from Yahoo Finance for {$symbol}: {$result['price']}");
                    return $result;
                }
            }
            
            Log::warning("Yahoo Finance API returned unsuccessful response for {$symbol}");
            return null;
            
        } catch (\Exception $e) {
            Log::error("Failed to fetch stock price from Yahoo Finance for {$symbol}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Convert local stock symbol to Yahoo Finance format
     */
    private function convertToYahooSymbol(string $symbol): string
    {
        // Yahoo Finance uses .JK suffix for Jakarta Stock Exchange
        return strtoupper($symbol) . '.JK';
    }

    /**
     * Get multiple stock prices at once
     */
    public function getMultipleStockPrices(array $symbols): array
    {
        $results = [];
        
        foreach ($symbols as $symbol) {
            $results[$symbol] = $this->getStockPrice($symbol);
            
            // Add small delay to avoid rate limiting
            usleep(100000); // 0.1 second delay
        }
        
        return $results;
    }

    /**
     * Update stock prices in database for all stock assets
     */
    public function updateAssetStockPrices(): array
    {
        $updatedAssets = [];
        
        try {
            // Get all stock assets (Paper Assets type)
            $stockAssets = \App\Models\FinancialAccount::where('type', 'like', 'AP%')
                ->where('is_active', true)
                ->where('is_group', false)
                ->get();

            foreach ($stockAssets as $account) {
                $symbol = $this->extractStockSymbol($account->name);
                
                if ($symbol) {
                    $priceData = $this->getStockPrice($symbol);
                    
                    if ($priceData && $priceData['price'] > 0) {
                        // Get related asset data
                        $asset = $account->assets()->first();
                        
                        if ($asset) {
                            // Calculate new market value
                            $measurementUnit = \App\Enums\Measurement::fromValue($asset->measurement_unit ?? 'lot');
                            $unitValue = $measurementUnit ? $measurementUnit->getUnitValue() : 100;
                            
                            $newMarketValue = $priceData['price'] * $asset->buy_quantity * $unitValue;
                            
                            // Update financial account balance
                            $oldBalance = $account->balance;
                            $account->update([
                                'balance' => $newMarketValue,
                                'updated_at' => now(),
                            ]);

                            $updatedAssets[] = [
                                'symbol' => $symbol,
                                'account_id' => $account->id,
                                'account_name' => $account->name,
                                'old_balance' => $oldBalance,
                                'new_balance' => $newMarketValue,
                                'price' => $priceData['price'],
                                'change' => $priceData['change'],
                                'change_percent' => $priceData['change_percent'],
                                'volume' => $priceData['volume'],
                                'market_state' => $priceData['market_state'],
                                'source' => $priceData['source'],
                            ];
                        }
                    }
                }
                
                // Small delay between requests
                usleep(200000); // 0.2 second delay
            }

        } catch (\Exception $e) {
            Log::error("Error updating stock prices: " . $e->getMessage());
        }

        return $updatedAssets;
    }

    /**
     * Extract stock symbol from account name
     */
    private function extractStockSymbol(string $accountName): ?string
    {
        // Extract symbol from account names like "Saham KEEN", "Saham PTPS", etc.
        if (preg_match('/saham\s+([a-z]{4})/i', $accountName, $matches)) {
            return strtoupper($matches[1]);
        }

        // Also handle formats like "KEEN Stock", "PTPS Shares", etc.
        if (preg_match('/^([a-z]{4})\s+(stock|shares?)/i', $accountName, $matches)) {
            return strtoupper($matches[1]);
        }

        return null;
    }

    /**
     * Get Indonesian stock market status
     */
    public function getMarketStatus(): array
    {
        // Use a major IDX stock to determine market status
        $priceData = $this->getStockPrice('BBCA');
        
        $marketHours = $this->getIDXMarketHours();
        $currentTime = now()->setTimezone('Asia/Jakarta');
        $isMarketDay = $currentTime->isWeekday();
        $isMarketHours = $currentTime->between($marketHours['open'], $marketHours['close']);
        
        return [
            'is_open' => $isMarketDay && $isMarketHours,
            'market_state' => $priceData['market_state'] ?? 'UNKNOWN',
            'current_time' => $currentTime->format('Y-m-d H:i:s T'),
            'market_hours' => $marketHours,
            'next_open' => $this->getNextMarketOpen(),
        ];
    }

    /**
     * Get IDX market hours
     */
    private function getIDXMarketHours(): array
    {
        return [
            'open' => now()->setTimezone('Asia/Jakarta')->setTime(9, 0, 0),
            'close' => now()->setTimezone('Asia/Jakarta')->setTime(16, 0, 0),
            'timezone' => 'Asia/Jakarta'
        ];
    }

    /**
     * Get next market open time
     */
    private function getNextMarketOpen(): string
    {
        $currentTime = now()->setTimezone('Asia/Jakarta');
        $marketHours = $this->getIDXMarketHours();
        
        if ($currentTime->isWeekday() && $currentTime->lt($marketHours['open'])) {
            // Today before market open
            return $marketHours['open']->format('Y-m-d H:i:s T');
        } else {
            // Next weekday
            $nextWeekday = $currentTime->copy()->addDay();
            while ($nextWeekday->isWeekend()) {
                $nextWeekday->addDay();
            }
            return $nextWeekday->setTime(9, 0, 0)->format('Y-m-d H:i:s T');
        }
    }

    /**
     * Test connection to Yahoo Finance
     */
    public function testConnection(): array
    {
        $testSymbol = 'BBCA'; // Use BBCA as test
        $startTime = microtime(true);
        
        $result = $this->getStockPrice($testSymbol);
        
        $endTime = microtime(true);
        $responseTime = round(($endTime - $startTime) * 1000, 2); // in milliseconds
        
        return [
            'success' => $result !== null,
            'response_time_ms' => $responseTime,
            'test_symbol' => $testSymbol,
            'test_data' => $result,
            'timestamp' => now(),
        ];
    }
}