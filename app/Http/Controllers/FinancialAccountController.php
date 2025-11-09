<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinancialAccount;

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
            $query = $request->user()->financialAccounts();
            if ($types) {
                $query->whereIn('type', $types);
            }
            $accounts = $query->get();
        } else {
            // Public: return all accounts filtered by type
            $query = FinancialAccount::query();
            if ($types) {
                $query->ofType($types);
            }
            $accounts = $query->get();
        }

        return response()->json([
            'data' => $accounts,
        ]);
    }
}
