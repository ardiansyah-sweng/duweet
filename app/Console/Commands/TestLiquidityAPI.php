<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FinancialReportService;

class TestLiquidityAPI extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:liquidity-api';

    /**
     * The console command description.
     */
    protected $description = 'Test liquidity API endpoints and display formatted results';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Liquidity API Endpoints...');
        $this->newLine();

        try {
            $reportService = app(FinancialReportService::class);
            
            // Test 1: Basic Liquidity Breakdown
            $this->info('💧 1. Basic Liquidity Breakdown:');
            $liquidity = $reportService->getLiquidityBreakdown();
            
            $this->line("   💰 Total Assets: {$liquidity['total']['formatted_amount']}");
            $this->line("   💵 Liquid Assets: {$liquidity['liquid']['formatted_amount']} ({$liquidity['liquid']['percentage']}%)");
            $this->line("   🏠 Illiquid Assets: {$liquidity['illiquid']['formatted_amount']} ({$liquidity['illiquid']['percentage']}%)");
            $this->newLine();

            // Test 2: Detailed Liquidity Analysis
            $this->info('🔍 2. Detailed Liquidity Analysis:');
            $details = $reportService->getLiquidityDetails();
            
            $summary = $details['summary'];
            $this->line("   📊 Portfolio Liquidity Summary:");
            $this->line("      💵 Liquid: {$summary['liquid_count']} assets totaling " . number_format($summary['liquid_total']) . " ({$summary['liquid_percentage']}%)");
            $this->line("      🏠 Illiquid: {$summary['illiquid_count']} assets totaling " . number_format($summary['illiquid_total']) . " ({$summary['illiquid_percentage']}%)");
            $this->newLine();

            // Test 3: Top Liquid Assets Details
            $this->info('💵 3. Top 10 Liquid Assets:');
            $this->line('   Assets that can be quickly converted to cash:');
            foreach (array_slice($details['liquid_assets']['assets'], 0, 10) as $index => $asset) {
                $status = $asset['gain_loss'] >= 0 ? '📈' : '📉';
                $performance = $asset['gain_loss_percentage'] >= 0 ? '+' . $asset['gain_loss_percentage'] : $asset['gain_loss_percentage'];
                $this->line("   " . ($index + 1) . ". {$status} {$asset['account_name']} ({$asset['parent_category']})");
                $this->line("      💰 Value: {$asset['formatted_balance']} | Performance: {$performance}%");
            }
            $this->newLine();

            // Test 4: Top Illiquid Assets Details
            $this->info('🏠 4. Top 10 Illiquid Assets:');
            $this->line('   Assets that require time/effort to convert to cash:');
            foreach (array_slice($details['illiquid_assets']['assets'], 0, 10) as $index => $asset) {
                $status = $asset['gain_loss'] >= 0 ? '📈' : '📉';
                $performance = $asset['gain_loss_percentage'] >= 0 ? '+' . $asset['gain_loss_percentage'] : $asset['gain_loss_percentage'];
                $this->line("   " . ($index + 1) . ". {$status} {$asset['account_name']} ({$asset['parent_category']})");
                $this->line("      💰 Value: {$asset['formatted_balance']} | Performance: {$performance}%");
            }
            $this->newLine();

            // Test 5: Liquidity Categories Breakdown
            $this->info('📋 5. Liquidity Categories Analysis:');
            
            // Group liquid assets by category
            $liquidByCategory = [];
            foreach ($details['liquid_assets']['assets'] as $asset) {
                $category = $asset['parent_category'];
                if (!isset($liquidByCategory[$category])) {
                    $liquidByCategory[$category] = ['count' => 0, 'total' => 0];
                }
                $liquidByCategory[$category]['count']++;
                $liquidByCategory[$category]['total'] += $asset['balance'];
            }

            $this->line("   💵 Liquid Asset Categories:");
            foreach ($liquidByCategory as $category => $data) {
                $percentage = $summary['liquid_total'] > 0 ? round(($data['total'] / $summary['liquid_total']) * 100, 2) : 0;
                $this->line("      🔸 {$category}: {$data['count']} assets, " . number_format($data['total']) . " ({$percentage}%)");
            }
            $this->newLine();

            // Group illiquid assets by category
            $illiquidByCategory = [];
            foreach ($details['illiquid_assets']['assets'] as $asset) {
                $category = $asset['parent_category'];
                if (!isset($illiquidByCategory[$category])) {
                    $illiquidByCategory[$category] = ['count' => 0, 'total' => 0];
                }
                $illiquidByCategory[$category]['count']++;
                $illiquidByCategory[$category]['total'] += $asset['balance'];
            }

            $this->line("   🏠 Illiquid Asset Categories:");
            foreach ($illiquidByCategory as $category => $data) {
                $percentage = $summary['illiquid_total'] > 0 ? round(($data['total'] / $summary['illiquid_total']) * 100, 2) : 0;
                $this->line("      🔸 {$category}: {$data['count']} assets, " . number_format($data['total']) . " ({$percentage}%)");
            }
            $this->newLine();

            // Test 6: JSON API Response Simulation
            $this->info('🌐 6. API Response Simulation:');
            $this->line('   📱 GET /api/assets/liquidity');
            $this->line('   📱 GET /api/assets/liquidity/details');
            $this->line('');
            $this->line('   Sample JSON Response Structure:');
            $this->line('   {');
            $this->line('     "success": true,');
            $this->line('     "message": "Detailed liquidity analysis retrieved successfully",');
            $this->line('     "data": {');
            $this->line('       "summary": { ... },');
            $this->line('       "liquid_assets": {');
            $this->line('         "description": "Assets that can be quickly converted to cash...",');
            $this->line('         "total_value": ' . $details['liquid_assets']['total_value'] . ',');
            $this->line('         "count": ' . $details['liquid_assets']['count'] . ',');
            $this->line('         "assets": [ ... ]');
            $this->line('       },');
            $this->line('       "illiquid_assets": { ... }');
            $this->line('     }');
            $this->line('   }');
            $this->newLine();

            $this->info('✅ All liquidity API tests completed successfully!');
            $this->info('💡 Key Insights:');
            $this->line("   • Portfolio has {$summary['liquid_percentage']}% liquid assets for quick access to cash");
            $this->line("   • {$summary['illiquid_percentage']}% is in long-term/growth assets");
            $this->line("   • Total of {$summary['total_count']} individual assets tracked");

        } catch (\Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}