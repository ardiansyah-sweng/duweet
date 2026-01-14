<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinancialAccount;
use App\Enums\AccountType;

class FinancialAccountController extends Controller
{
    /**
     * Return financial accounts filtered by type.
     * Query param: ?type=AS or ?type=AS,LI or ?type=IN
     * If user is authenticated, returns only accounts linked to the user.
     */
    public function index(Request $request)
    {
        $type = $request->query('type');
        $summary = $request->query('summary', false);

        // normalize types into array or null
        $types = null;
        if ($type) {
            if (is_array($type)) {
                $types = $type;
            } else {
                $types = array_filter(array_map('trim', explode(',', $type)));
            }
        }

        // If the user is authenticated, prefer user's linked accounts
        if ($request->user()) {
            if ($types) {
                $accounts = $request->user()->getAccountsByType($types);
            } else {
                $accounts = $request->user()->getActiveAccounts();
            }
            
            $responseData = [
                'source' => 'user_accounts',
                'user_id' => $request->user()->id,
                'data' => $accounts,
                'count' => count($accounts),
            ];

            if ($summary) {
                $responseData['summary'] = $request->user()->getAccountsSummary();
            }
        } else {
            // Public: return all accounts filtered by type
            $query = FinancialAccount::query();
            if ($types) {
                $query->ofType($types);
            }
            $accounts = $query->active()->get();

            $responseData = [
                'source' => 'all_accounts',
                'data' => $accounts,
                'count' => count($accounts),
                'filter' => $types ? implode(',', $types) : 'all',
            ];

            if ($summary) {
                $responseData['summary'] = FinancialAccount::summaryByType();
            }
        }

        return response()->json($responseData);
    }

    /**
     * Get account types reference
     */
    public function types()
    {
        $types = collect(AccountType::cases())->map(fn($case) => [
            'value' => $case->value,
            'name' => $case->name,
            'label' => $case->label(),
        ]);

        return response()->json([
            'data' => $types,
            'count' => count($types),
        ]);
    }

    /**
     * Get account summary grouped by type
     */
    public function summary(Request $request)
    {
        if ($request->user()) {
            $summary = $request->user()->getAccountsSummary();
            return response()->json([
                'source' => 'user',
                'user_id' => $request->user()->id,
                'data' => $summary,
            ]);
        }

        $summary = FinancialAccount::summaryByType();
        return response()->json([
            'source' => 'all_accounts',
            'data' => $summary,
        ]);
    }
}
