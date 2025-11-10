<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FinancialReportService;
use App\Models\FinancialAccount;

class TestAssetReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:asset-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test asset reporting functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Testing Asset Reporting System...');
        $this->newLine();

        try {
            // Test 1: Basic Asset Balance
            $this->info('📊 1. Testing Basic Asset Balance:');
            $totalAssets = FinancialAccount::getTotalAssetBalance();
            $this->line("   Total Assets: Rp " . number_format($totalAssets, 0, ',', '.'));
            
            $balanceByType = FinancialAccount::getAssetBalanceByType();
            foreach ($balanceByType as $type => $balance) {
                $this->line("   - {$type}: Rp " . number_format($balance, 0, ',', '.'));
            }
            $this->newLine();

            // Test 2: Asset Hierarchy
            $this->info('🏗️ 2. Testing Asset Hierarchy:');
            $hierarchy = FinancialAccount::getAssetHierarchy();
            foreach ($hierarchy as $account) {
                $this->line("   📁 {$account['name']} ({$account['type']})");
                $this->line("      Balance: Rp " . number_format($account['balance'], 0, ',', '.'));
                if ($account['children']) {
                    foreach ($account['children'] as $childName => $child) {
                        $this->line("      └── {$childName}: Rp " . number_format($child['balance'], 0, ',', '.'));
                    }
                }
            }
            $this->newLine();

            // Test 3: Service Layer
            $reportService = app(FinancialReportService::class);
            
            $this->info('📈 3. Testing Performance Analytics:');
            $performance = $reportService->getAssetPerformance();
            foreach (array_slice($performance, 0, 3) as $asset) {
                $status = $asset['performance_status'] === 'profit' ? '📈' : '📉';
                $this->line("   {$status} {$asset['account_name']}: {$asset['gain_loss_percentage']}%");
            }
            $this->newLine();

            // Test 4: Liquidity Breakdown
            $this->info('💧 4. Testing Liquidity Breakdown:');
            $liquidity = $reportService->getLiquidityBreakdown();
            $this->line("   💵 Liquid Assets: {$liquidity['liquid']['formatted_amount']} ({$liquidity['liquid']['percentage']}%)");
            $this->line("   🏠 Illiquid Assets: {$liquidity['illiquid']['formatted_amount']} ({$liquidity['illiquid']['percentage']}%)");
            $this->newLine();

            // Test 4b: Detailed Liquidity Analysis
            $this->info('💧 4b. Testing Detailed Liquidity Analysis:');
            $liquidityDetails = $reportService->getLiquidityDetails();
            
            $this->line("   📊 Summary:");
            $this->line("      💵 Liquid: {$liquidityDetails['summary']['liquid_count']} assets, " . 
                       number_format($liquidityDetails['summary']['liquid_total']) . " ({$liquidityDetails['summary']['liquid_percentage']}%)");
            $this->line("      🏠 Illiquid: {$liquidityDetails['summary']['illiquid_count']} assets, " . 
                       number_format($liquidityDetails['summary']['illiquid_total']) . " ({$liquidityDetails['summary']['illiquid_percentage']}%)");
            
            $this->line("   💵 Top Liquid Assets:");
            foreach (array_slice($liquidityDetails['liquid_assets']['assets'], 0, 5) as $asset) {
                $status = $asset['gain_loss'] >= 0 ? '📈' : '📉';
                $this->line("      {$status} {$asset['account_name']}: {$asset['formatted_balance']} ({$asset['gain_loss_percentage']}%)");
            }
            
            $this->line("   🏠 Top Illiquid Assets:");
            foreach (array_slice($liquidityDetails['illiquid_assets']['assets'], 0, 5) as $asset) {
                $status = $asset['gain_loss'] >= 0 ? '📈' : '📉';
                $this->line("      {$status} {$asset['account_name']}: {$asset['formatted_balance']} ({$asset['gain_loss_percentage']}%)");
            }
            $this->newLine();

            // Test 5: Asset Allocation
            $this->info('🥧 5. Testing Asset Allocation:');
            $allocation = $reportService->getAssetAllocation();
            foreach ($allocation['allocation'] as $asset) {
                $this->line("   📊 {$asset['type']}: {$asset['formatted_balance']} ({$asset['percentage']}%)");
            }
            $this->newLine();

            // Test 6: Dashboard Summary
            $this->info('🎯 6. Testing Dashboard Summary:');
            $dashboard = $reportService->getAssetDashboard();
            $overview = $dashboard['overview'];
            $this->line("   💰 Total Assets: {$overview['formatted_total_assets']}");
            $this->line("   📊 Total Accounts: {$overview['total_accounts']}");
            $this->line("   📈 Overall Performance: {$overview['overall_gain_loss_percentage']}% ({$overview['performance_status']})");
            $this->newLine();

            $this->info('✅ All tests completed successfully!');
            $this->info('🌐 API Endpoints available at:');
            $this->line('   GET /api/assets/summary');
            $this->line('   GET /api/assets/dashboard');
            $this->line('   GET /api/assets/report');
            $this->line('   GET /api/assets/performance');
            $this->line('   GET /api/assets/allocation');
            $this->line('   GET /api/assets/liquidity');
            $this->line('   GET /api/assets/liquidity/details');
            $this->line('   GET /api/assets/hierarchy');
            $this->line('   GET /api/assets/top-performers');
            $this->line('   GET /api/assets/export');
            $this->line('   GET /api/assets/account/{id}');

        } catch (\Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}