<?php

namespace App\Http\Controllers;

use App\Services\FinancialReportService;
use App\Models\FinancialAccount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AssetReportController extends Controller
{
    protected FinancialReportService $reportService;

    public function __construct(FinancialReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Get asset summary
     * 
     * @return JsonResponse
     */
    public function getAssetSummary(): JsonResponse
    {
        try {
            $totalAssets = FinancialAccount::getTotalAssetBalance();
            $balanceByType = FinancialAccount::getAssetBalanceByType();

            return response()->json([
                'success' => true,
                'message' => 'Asset summary retrieved successfully',
                'data' => [
                    'total_assets' => $totalAssets,
                    'formatted_total' => 'Rp ' . number_format($totalAssets, 0, ',', '.'),
                    'balance_by_type' => $balanceByType,
                    'generated_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve asset summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comprehensive asset report
     * 
     * @return JsonResponse
     */
    public function getAssetReport(): JsonResponse
    {
        try {
            $report = $this->reportService->getAssetReport();

            return response()->json([
                'success' => true,
                'message' => 'Asset report generated successfully',
                'data' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate asset report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get asset performance metrics
     * 
     * @return JsonResponse
     */
    public function getAssetPerformance(): JsonResponse
    {
        try {
            $performance = $this->reportService->getAssetPerformance();
            $liquidity = $this->reportService->getLiquidityBreakdown();

            return response()->json([
                'success' => true,
                'message' => 'Asset performance metrics retrieved successfully',
                'data' => [
                    'performance' => $performance,
                    'liquidity_breakdown' => $liquidity
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve asset performance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get asset allocation breakdown
     * 
     * @return JsonResponse
     */
    public function getAssetAllocation(): JsonResponse
    {
        try {
            $allocation = $this->reportService->getAssetAllocation();

            return response()->json([
                'success' => true,
                'message' => 'Asset allocation retrieved successfully',
                'data' => $allocation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve asset allocation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get liquidity breakdown (summary)
     * 
     * @return JsonResponse
     */
    public function getLiquidityBreakdown(): JsonResponse
    {
        try {
            $liquidity = $this->reportService->getLiquidityBreakdown();

            return response()->json([
                'success' => true,
                'message' => 'Liquidity breakdown retrieved successfully',
                'data' => $liquidity
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve liquidity breakdown',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed liquidity analysis with asset lists
     * 
     * @return JsonResponse
     */
    public function getLiquidityDetails(): JsonResponse
    {
        try {
            $liquidityDetails = $this->reportService->getLiquidityDetails();

            return response()->json([
                'success' => true,
                'message' => 'Detailed liquidity analysis retrieved successfully',
                'data' => $liquidityDetails
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve liquidity details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get top performing assets
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getTopPerformingAssets(Request $request): JsonResponse
    {
        try {
            $limit = $request->query('limit', 5);
            $topPerformers = $this->reportService->getTopPerformingAssets($limit);

            return response()->json([
                'success' => true,
                'message' => 'Top performing assets retrieved successfully',
                'data' => $topPerformers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve top performing assets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comprehensive asset dashboard
     * 
     * @return JsonResponse
     */
    public function getAssetDashboard(): JsonResponse
    {
        try {
            $dashboard = $this->reportService->getAssetDashboard();

            return response()->json([
                'success' => true,
                'message' => 'Asset dashboard data retrieved successfully',
                'data' => $dashboard
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve asset dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get asset hierarchy (tree structure)
     * 
     * @return JsonResponse
     */
    public function getAssetHierarchy(): JsonResponse
    {
        try {
            $hierarchy = FinancialAccount::getAssetHierarchy();

            return response()->json([
                'success' => true,
                'message' => 'Asset hierarchy retrieved successfully',
                'data' => $hierarchy
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve asset hierarchy',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get asset trends (placeholder for future historical data)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getAssetTrends(Request $request): JsonResponse
    {
        try {
            $days = $request->query('days', 30);
            $trends = $this->reportService->getAssetTrends($days);

            return response()->json([
                'success' => true,
                'message' => 'Asset trends data retrieved',
                'data' => $trends
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve asset trends',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export asset data
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function exportAssetData(Request $request): JsonResponse
    {
        try {
            $format = $request->query('format', 'json'); // json, csv, excel
            $exportData = $this->reportService->exportAssetData();

            if ($format === 'csv') {
                // Convert to CSV format
                $filename = 'asset_export_' . now()->format('Y_m_d_H_i_s') . '.csv';
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                ];

                $callback = function() use ($exportData) {
                    $file = fopen('php://output', 'w');
                    
                    // Write headers
                    if (!empty($exportData['data'])) {
                        fputcsv($file, array_keys($exportData['data'][0]));
                        
                        // Write data
                        foreach ($exportData['data'] as $row) {
                            fputcsv($file, $row);
                        }
                    }
                    
                    fclose($file);
                };

                return response()->stream($callback, 200, $headers);
            }

            return response()->json([
                'success' => true,
                'message' => 'Asset data exported successfully',
                'data' => $exportData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export asset data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific account details with balance calculation
     * 
     * @param int $accountId
     * @return JsonResponse
     */
    public function getAccountDetails(int $accountId): JsonResponse
    {
        try {
            $account = FinancialAccount::assetAccounts()
                ->activeAccounts()
                ->with(['parent', 'children', 'assets'])
                ->findOrFail($accountId);

            $accountData = [
                'account_info' => [
                    'id' => $account->id,
                    'name' => $account->name,
                    'type' => $account->type->value,
                    'is_group' => $account->is_group,
                    'level' => $account->level,
                    'sort_order' => $account->sort_order,
                    'description' => $account->description
                ],
                'balance_info' => [
                    'current_balance' => $account->getTotalBalance(),
                    'formatted_balance' => $account->getFormattedBalance(),
                    'initial_balance' => $account->initial_balance,
                    'gain_loss' => $account->getGainLoss()
                ],
                'hierarchy' => [
                    'parent' => $account->parent ? [
                        'id' => $account->parent->id,
                        'name' => $account->parent->name
                    ] : null,
                    'path' => $account->getAccountPath(),
                    'children_count' => $account->children->count()
                ],
                'assets' => $account->assets->map(function($asset) {
                    return [
                        'id' => $asset->id,
                        'acquisition_date' => $asset->acquisition_date,
                        'bought_price' => $asset->bought_price,
                        'buy_quantity' => $asset->buy_quantity,
                        'measurement_unit' => $asset->measurement_unit,
                        'is_liquid' => $asset->is_liquid,
                        'is_productive' => $asset->is_productive
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'message' => 'Account details retrieved successfully',
                'data' => $accountData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve account details',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}