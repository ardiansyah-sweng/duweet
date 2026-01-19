<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
public function groupBalance()
{
    $data = \DB::table('financial_accounts')
        ->select(
            'type as account_type',
            \DB::raw('SUM(initial_balance) as total_balance')
        )
        ->groupBy('type')
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $data
    ]);
}
}
