<?php

namespace App\Services;

use App\Models\FinancialAccount;
use App\Constants\FinancialAccountColumns as Cols;
use Illuminate\Support\Collection;

class FinancialReportService
{
    /**
     * Get comprehensive asset report
     */
    public function getAssetReport(): array
    {
        $assetAccounts = FinancialAccount::assetAccounts()
            ->activeAccounts()
            ->groupAccounts()
            ->with(['children' => function ($query) {
                $query->where(Cols::IS_ACTIVE, true)
                      ->orderBy(Cols::SORT_ORDER);
            }])
            ->orderBy(Cols::SORT_ORDER)
            ->get();

        $report = [
            'summary' => [
                'total_assets' => 0,
                'by_category' => []
            ],
            'details' => []
        ];

        foreach ($assetAccounts as $account) {
            $categoryBalance = $account->getTotalBalance();
            
            $report['summary']['total_assets'] += $categoryBalance;
            $report['summary']['by_category'][$account->{Cols::NAME}] = $categoryBalance;
            
            $report['details'][$account->{Cols::NAME}] = [
                'account_info' => [
                    'id' => $account->{Cols::ID},
                    'name' => $account->{Cols::NAME},
                    'type' => $account->{Cols::TYPE}->value,
                    'level' => $account->{Cols::LEVEL}
                ],
                'balance' => $categoryBalance,
                'breakdown' => $account->getBalanceBreakdown()
            ];
        }

        return $report;
    }

    /**
     * Get asset balance with performance metrics
     */
    public function getAssetPerformance(): array
    {
        $assets = FinancialAccount::assetAccounts()
            ->activeAccounts()
            ->leafAccounts()
            ->get();

        $performance = [];
        
        foreach ($assets as $asset) {
            $gainLoss = $asset->{Cols::BALANCE} - $asset->{Cols::INITIAL_BALANCE};
            $gainLossPercentage = $asset->{Cols::INITIAL_BALANCE} > 0 
                ? ($gainLoss / $asset->{Cols::INITIAL_BALANCE}) * 100 
                : 0;

            $performance[] = [
                'account_id' => $asset->{Cols::ID},
                'account_name' => $asset->{Cols::NAME},
                'account_type' => $asset->{Cols::TYPE}->value,
                'current_balance' => $asset->{Cols::BALANCE},
                'initial_balance' => $asset->{Cols::INITIAL_BALANCE},
                'gain_loss' => $gainLoss,
                'gain_loss_percentage' => round($gainLossPercentage, 2),
                'performance_status' => $gainLoss >= 0 ? 'profit' : 'loss',
                'formatted_balance' => $asset->getFormattedBalance(),
                'is_liquid' => $asset->isLiquid()
            ];
        }

        // Sort by performance (best performers first)
        usort($performance, function ($a, $b) {
            return $b['gain_loss_percentage'] <=> $a['gain_loss_percentage'];
        });

        return $performance;
    }

    /**
     * Get liquid vs illiquid assets breakdown
     */
    public function getLiquidityBreakdown(): array
    {
        // Get all leaf assets (actual assets with data, not group accounts)
        $allAssets = FinancialAccount::assetAccounts()
            ->activeAccounts()
            ->leafAccounts()
            ->get();

        $liquidAssets = 0;
        $illiquidAssets = 0;

        foreach ($allAssets as $asset) {
            // Check if asset has associated asset data with liquidity info
            $assetData = \DB::table(config('db_tables.asset', 'assets'))
                ->where(\App\Constants\AssetColumns::ACCOUNT_ID, $asset->id)
                ->first();

            if ($assetData && $assetData->is_liquid) {
                $liquidAssets += $asset->balance;
            } else {
                $illiquidAssets += $asset->balance;
            }
        }

        $total = $liquidAssets + $illiquidAssets;

        return [
            'liquid' => [
                'amount' => $liquidAssets,
                'percentage' => $total > 0 ? round(($liquidAssets / $total) * 100, 2) : 0,
                'formatted_amount' => 'Rp ' . number_format($liquidAssets, 0, ',', '.')
            ],
            'illiquid' => [
                'amount' => $illiquidAssets,
                'percentage' => $total > 0 ? round(($illiquidAssets / $total) * 100, 2) : 0,
                'formatted_amount' => 'Rp ' . number_format($illiquidAssets, 0, ',', '.')
            ],
            'total' => [
                'amount' => $total,
                'formatted_amount' => 'Rp ' . number_format($total, 0, ',', '.')
            ]
        ];
    }

    /**
     * Get detailed breakdown of liquid and illiquid assets
     */
    public function getLiquidityDetails(): array
    {
        // Get all leaf assets (actual assets with data, not group accounts)
        $allAssets = FinancialAccount::assetAccounts()
            ->activeAccounts()
            ->leafAccounts()
            ->with('parent')
            ->get();

        $liquidAssets = [];
        $illiquidAssets = [];
        $liquidTotal = 0;
        $illiquidTotal = 0;

        foreach ($allAssets as $asset) {
            // Check if asset has associated asset data with liquidity info
            $assetData = \DB::table(config('db_tables.asset', 'assets'))
                ->where(\App\Constants\AssetColumns::ACCOUNT_ID, $asset->id)
                ->first();

            $assetInfo = [
                'account_id' => $asset->id,
                'account_name' => $asset->name,
                'parent_category' => $asset->parent?->name ?? 'Unknown',
                'account_type' => $asset->type->value,
                'balance' => $asset->balance,
                'formatted_balance' => 'Rp ' . number_format($asset->balance, 0, ',', '.'),
                'initial_balance' => $asset->initial_balance,
                'gain_loss' => $asset->balance - $asset->initial_balance,
                'gain_loss_percentage' => $asset->initial_balance > 0 
                    ? round((($asset->balance - $asset->initial_balance) / $asset->initial_balance) * 100, 2) 
                    : 0,
                'acquisition_date' => $assetData?->acquisition_date ?? null,
                'is_productive' => $assetData?->is_productive ?? false
            ];

            if ($assetData && $assetData->is_liquid) {
                $liquidAssets[] = $assetInfo;
                $liquidTotal += $asset->balance;
            } else {
                $illiquidAssets[] = $assetInfo;
                $illiquidTotal += $asset->balance;
            }
        }

        // Sort by balance (highest first)
        usort($liquidAssets, function ($a, $b) {
            return $b['balance'] <=> $a['balance'];
        });

        usort($illiquidAssets, function ($a, $b) {
            return $b['balance'] <=> $a['balance'];
        });

        $total = $liquidTotal + $illiquidTotal;

        return [
            'summary' => [
                'liquid_total' => $liquidTotal,
                'liquid_percentage' => $total > 0 ? round(($liquidTotal / $total) * 100, 2) : 0,
                'liquid_count' => count($liquidAssets),
                'illiquid_total' => $illiquidTotal,
                'illiquid_percentage' => $total > 0 ? round(($illiquidTotal / $total) * 100, 2) : 0,
                'illiquid_count' => count($illiquidAssets),
                'total_assets' => $total,
                'total_count' => count($liquidAssets) + count($illiquidAssets)
            ],
            'liquid_assets' => [
                'description' => 'Assets that can be quickly converted to cash with minimal price impact',
                'total_value' => $liquidTotal,
                'formatted_total' => 'Rp ' . number_format($liquidTotal, 0, ',', '.'),
                'count' => count($liquidAssets),
                'assets' => $liquidAssets
            ],
            'illiquid_assets' => [
                'description' => 'Assets that require significant time or effort to convert to cash',
                'total_value' => $illiquidTotal,
                'formatted_total' => 'Rp ' . number_format($illiquidTotal, 0, ',', '.'),
                'count' => count($illiquidAssets),
                'assets' => $illiquidAssets
            ]
        ];
    }

    /**
     * Get asset allocation by type
     */
    public function getAssetAllocation(): array
    {
        $assetTypes = FinancialAccount::assetAccounts()
            ->activeAccounts()
            ->groupAccounts()
            ->with('children')
            ->get();

        $totalAssets = FinancialAccount::getTotalAssetBalance();
        $allocation = [];

        foreach ($assetTypes as $assetType) {
            $typeBalance = $assetType->getTotalBalance();
            $percentage = $totalAssets > 0 ? ($typeBalance / $totalAssets) * 100 : 0;

            $allocation[] = [
                'type' => $assetType->{Cols::NAME},
                'type_code' => $assetType->{Cols::TYPE}->value,
                'balance' => $typeBalance,
                'percentage' => round($percentage, 2),
                'formatted_balance' => 'Rp ' . number_format($typeBalance, 0, ',', '.'),
                'accounts_count' => $assetType->getLeafDescendants()->count()
            ];
        }

        // Sort by balance (highest first)
        usort($allocation, function ($a, $b) {
            return $b['balance'] <=> $a['balance'];
        });

        return [
            'allocation' => $allocation,
            'total_assets' => $totalAssets,
            'formatted_total' => 'Rp ' . number_format($totalAssets, 0, ',', '.')
        ];
    }

    /**
     * Get top performing assets
     */
    public function getTopPerformingAssets(int $limit = 5): array
    {
        $performance = $this->getAssetPerformance();
        
        return [
            'top_gainers' => array_slice(
                array_filter($performance, fn($asset) => $asset['gain_loss'] > 0), 
                0, 
                $limit
            ),
            'top_losers' => array_slice(
                array_filter($performance, fn($asset) => $asset['gain_loss'] < 0), 
                -$limit
            )
        ];
    }

    /**
     * Get asset summary dashboard data
     */
    public function getAssetDashboard(): array
    {
        $totalAssets = FinancialAccount::getTotalAssetBalance();
        $assetAllocation = $this->getAssetAllocation();
        $liquidityBreakdown = $this->getLiquidityBreakdown();
        $liquidityDetails = $this->getLiquidityDetails();
        $topPerformers = $this->getTopPerformingAssets(3);
        
        // Calculate overall performance
        $allAssets = FinancialAccount::assetAccounts()
            ->activeAccounts()
            ->leafAccounts()
            ->get();
        
        $totalCurrentBalance = $allAssets->sum(Cols::BALANCE);
        $totalInitialBalance = $allAssets->sum(Cols::INITIAL_BALANCE);
        $overallGainLoss = $totalCurrentBalance - $totalInitialBalance;
        $overallGainLossPercentage = $totalInitialBalance > 0 
            ? ($overallGainLoss / $totalInitialBalance) * 100 
            : 0;

        return [
            'overview' => [
                'total_assets' => $totalAssets,
                'formatted_total_assets' => 'Rp ' . number_format($totalAssets, 0, ',', '.'),
                'total_accounts' => $allAssets->count(),
                'overall_gain_loss' => $overallGainLoss,
                'overall_gain_loss_percentage' => round($overallGainLossPercentage, 2),
                'performance_status' => $overallGainLoss >= 0 ? 'profit' : 'loss'
            ],
            'allocation' => $assetAllocation,
            'liquidity' => $liquidityBreakdown,
            'liquidity_details' => $liquidityDetails,
            'top_performers' => $topPerformers,
            'recent_update' => now()->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Get asset trends (requires historical data - placeholder for future implementation)
     */
    public function getAssetTrends(int $days = 30): array
    {
        // This would require a separate table to track historical balances
        // For now, return current state
        return [
            'message' => 'Asset trends feature requires historical data tracking',
            'current_snapshot' => [
                'date' => now()->format('Y-m-d'),
                'total_assets' => FinancialAccount::getTotalAssetBalance(),
                'asset_count' => FinancialAccount::assetAccounts()->activeAccounts()->leafAccounts()->count()
            ],
            'recommendation' => 'Implement AssetHistory model to track balance changes over time'
        ];
    }

    /**
     * Export asset data for external analysis
     */
    public function exportAssetData(): array
    {
        $assets = FinancialAccount::assetAccounts()
            ->activeAccounts()
            ->leafAccounts()
            ->with('parent')
            ->get();

        $exportData = [];
        
        foreach ($assets as $asset) {
            $exportData[] = [
                'account_id' => $asset->{Cols::ID},
                'account_name' => $asset->{Cols::NAME},
                'account_type' => $asset->{Cols::TYPE}->value,
                'parent_account' => $asset->parent?->{Cols::NAME},
                'current_balance' => $asset->{Cols::BALANCE},
                'initial_balance' => $asset->{Cols::INITIAL_BALANCE},
                'gain_loss' => $asset->{Cols::BALANCE} - $asset->{Cols::INITIAL_BALANCE},
                'gain_loss_percentage' => $asset->{Cols::INITIAL_BALANCE} > 0 
                    ? round((($asset->{Cols::BALANCE} - $asset->{Cols::INITIAL_BALANCE}) / $asset->{Cols::INITIAL_BALANCE}) * 100, 2) 
                    : 0,
                'is_liquid' => $asset->isLiquid(),
                'created_at' => $asset->{Cols::CREATED_AT}->format('Y-m-d H:i:s'),
                'updated_at' => $asset->{Cols::UPDATED_AT}->format('Y-m-d H:i:s')
            ];
        }

        return [
            'export_date' => now()->format('Y-m-d H:i:s'),
            'total_records' => count($exportData),
            'data' => $exportData
        ];
    }
}