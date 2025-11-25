<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use App\Constants\FinancialAccountColumns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialAccountController extends Controller
{
   
    public function index(Request $request)
    {
        $q = $request->query('q');

        $filters = [
            'type' => $request->query('type'),
            'is_active' => $request->has('is_active') ? filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null,
            'parent_id' => $request->query('parent_id'),
            'level' => $request->query('level'),
        ];

        $sortBy = $request->query('sort_by', FinancialAccountColumns::SORT_ORDER);
        $order = $request->query('order', 'asc');
        $perPage = (int) $request->query('per_page', 15);

        $table = config('db_tables.financial_account', 'financial_accounts');

        $builder = DB::table($table)->select("{$table}.*");
        if ($q !== null && trim($q) !== '') {
            $term = '%' . trim($q) . '%';
            $builder->whereRaw("({$table}." . FinancialAccountColumns::NAME . " LIKE ? OR {$table}." . FinancialAccountColumns::DESCRIPTION . " LIKE ?)", [$term, $term]);
        }

        if (isset($filters['type']) && $filters['type'] !== null) {
            $builder->where(FinancialAccountColumns::TYPE, $filters['type']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== null) {
            $builder->where(FinancialAccountColumns::IS_ACTIVE, (bool) $filters['is_active']);
        }

        if (isset($filters['parent_id']) && $filters['parent_id'] !== null) {
            $builder->where(FinancialAccountColumns::PARENT_ID, $filters['parent_id']);
        }

        if (isset($filters['level']) && $filters['level'] !== null) {
            $builder->where(FinancialAccountColumns::LEVEL, $filters['level']);
        }

        $builder->orderBy($sortBy, $order);

        $page = $builder->paginate($perPage)->appends($request->query());

        $items = collect($page->items())->map(function ($row) {
            return (array) $row;
        })->toArray();

        if (!empty($items)) {
            $ids = array_column($items, FinancialAccountColumns::ID);
            $childrenRows = DB::table($table)
                ->whereIn(FinancialAccountColumns::PARENT_ID, $ids)
                ->orderBy(FinancialAccountColumns::SORT_ORDER)
                ->get()
                ->map(function ($r) { return (array) $r; })
                ->toArray();


            $grouped = [];
            foreach ($childrenRows as $c) {
                $parentId = $c[FinancialAccountColumns::PARENT_ID];
                $grouped[$parentId][] = $c;
            }

            // attach children to items
            foreach ($items as &$it) {
                $it['children'] = $grouped[$it[FinancialAccountColumns::ID]] ?? [];
            }
            unset($it);
        }

        return response()->json([
            'success' => true,
            'data' => $items,
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
