<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use App\Constants\FinancialAccountColumns;
use Illuminate\Http\Request;

class FinancialAccountController extends Controller
{
    /**
     * API: list / search financial accounts
     *
     * Query params supported:
     * - q: search term (name or description)
     * - type: account type
     * - is_active: boolean
     * - parent_id: integer
     * - level: integer
     * - sort_by: column name (defaults to sort_order)
     * - order: asc|desc
     * - per_page: int
     */
    public function index(Request $request)
    {
        $q = $request->query('q');

        $filters = [
            'type' => $request->query('type'),
            // Accept both 'true'/'false' strings and boolean values
            'is_active' => $request->has('is_active') ? filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null,
            'parent_id' => $request->query('parent_id'),
            'level' => $request->query('level'),
        ];

        $sortBy = $request->query('sort_by', FinancialAccountColumns::SORT_ORDER);
        $order = $request->query('order', 'asc');
        $perPage = (int) $request->query('per_page', 15);

        $query = FinancialAccount::query()
            ->with('children')
            ->search($q)
            ->applyFilters($filters)
            ->orderBy($sortBy, $order);

        $page = $query->paginate($perPage)->appends($request->query());

        return response()->json([
            'success' => true,
            'data' => $page->items(),
            'meta' => [
                'current_page' => $page->currentPage(),
                'last_page' => $page->lastPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
            ],
        ]);
    }

    /**
     * Show single financial account
     */
    public function show($id)
    {
        $account = FinancialAccount::with('children', 'parent')->find($id);

        if (!$account) {
            return response()->json(['success' => false, 'message' => 'FinancialAccount tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $account]);
    }
}
