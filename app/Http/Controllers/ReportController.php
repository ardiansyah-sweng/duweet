<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Constants\UserFinancialAccountColumns as UFA;
use App\Constants\FinancialAccountColumns as FA;

class ReportController extends Controller
{
    public function liquidAssets(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $userId = $request->user_id;

        // total saldo semua akun bertipe 'AS' milik user
        $total = DB::table(UFA::TABLE . ' as ufa')
            ->join(FA::TABLE . ' as fa', 'fa.' . FA::ID, '=', 'ufa.' . UFA::FINANCIAL_ACCOUNT_ID)
            ->where('ufa.' . UFA::USER_ID, $userId)
            ->where('fa.' . FA::TYPE, FA::TYPE_ASSET)
            ->sum('ufa.' . UFA::BALANCE);

        $accounts = DB::table(UFA::TABLE . ' as ufa')
            ->join(FA::TABLE . ' as fa', 'fa.' . FA::ID, '=', 'ufa.' . UFA::FINANCIAL_ACCOUNT_ID)
            ->where('ufa.' . UFA::USER_ID, $userId)
            ->where('fa.' . FA::TYPE, FA::TYPE_ASSET)
            ->select('fa.' . FA::NAME . ' as account_name', 'ufa.' . UFA::BALANCE . ' as balance')
            ->get();

        return response()->json([
            'user_id' => $userId,
            'total_liquid_assets' => $total,
            'accounts' => $accounts,
        ], 200);
    }
}
