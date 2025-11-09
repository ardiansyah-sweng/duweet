<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FinancialAccount;
use App\Http\Requests\FinancialAccountSearchRequest;
use App\Http\Resources\FinancialAccountResource;

class FinancialAccountController extends Controller
{
    /**
     * Display a listing of financial accounts with search and filters.
     *
     * Query params: q, type, is_active, level, parent_id, min_balance, max_balance, per_page, sort
     */
    public function index(FinancialAccountSearchRequest $request)
    {
        $q = $request->input('q');
        $filters = $request->only([
            'type','is_active','level','parent_id','min_balance','max_balance'
        ]);

        $perPage = (int) $request->input('per_page', 20);
        $sort = $request->input('sort', 'sort_order');

        $query = FinancialAccount::query()
            ->with('children')
            ->search($q)
            ->filter($filters)
            ->orderBy($sort)
            ->orderBy('name');

        $paginated = $query->paginate($perPage)->appends($request->query());

        // Return resource collection (will include pagination meta)
        return FinancialAccountResource::collection($paginated);
    }
}
