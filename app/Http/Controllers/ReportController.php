<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Constants\TransactionColumns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Routing\Controller;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Hitung surplus / defisit user berdasarkan periode
     *
     * GET /api/reports/surplus-deficit/{userId}
     * Query:
     *  - start_date (Y-m-d) REQUIRED
     *  - end_date   (Y-m-d) REQUIRED
     *  - user_account_id (optional)
     */
    public function getUserSurplusDeficit(Request $request, int $userId)
    {
        /** ================= VALIDATION ================= */
        try {
            $request->validate([
                'start_date' => 'required|date_format:Y-m-d',
                'end_date'   => 'required|date_format:Y-m-d|after_or_equal:start_date',
                'user_account_id' => 'nullable|integer|exists:user_accounts,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate   = Carbon::parse($request->end_date)->endOfDay();

        /** ================= USER ================= */
        $user = User::select('id', 'name', 'email')->find($userId);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        /** ================= USER ACCOUNT ================= */
        $userAccountId = $request->user_account_id;

        if ($userAccountId) {
            $userAccount = DB::table('user_accounts')
                ->where('id', $userAccountId)
                ->where('id_user', $userId)
                ->first();
        } else {
            $userAccount = DB::table('user_accounts')
                ->where('id_user', $userId)
                ->first();
            $userAccountId = $userAccount->id ?? null;
        }

        if (!$userAccountId) {
            return response()->json([
                'success' => false,
                'message' => 'No user accounts found for this user.',
            ], 404);
        }

        /** ================= SURPLUS / DEFICIT QUERY ================= */
        $result = DB::table('transactions as t')
            ->join('financial_accounts as fa', 't.financial_account_id', '=', 'fa.id')
            ->where('t.user_account_id', $userAccountId)
            ->whereBetween('t.created_at', [$startDate, $endDate])
            ->selectRaw("
                SUM(
                    CASE 
                        WHEN fa.type = 'IN' AND t.balance_effect = 'increase'
                        THEN t.amount ELSE 0 
                    END
                ) AS total_income,

                SUM(
                    CASE 
                        WHEN fa.type = 'EX' AND t.balance_effect = 'decrease'
                        THEN t.amount ELSE 0 
                    END
                ) AS total_expense
            ")
            ->first();

        $totalIncome  = (float) ($result->total_income ?? 0);
        $totalExpense = (float) ($result->total_expense ?? 0);
        $surplusDeficit = $totalIncome - $totalExpense;

        /** ================= RESPONSE ================= */
        return response()->json([
            'success' => true,
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'user_account' => [
                'id'       => $userAccount->id,
                'username' => $userAccount->username ?? null,
                'email'    => $userAccount->email ?? null,
            ],
            'period' => [
                'start_date' => $startDate->toDateString(),
                'end_date'   => $endDate->toDateString(),
            ],
            'summary' => [
                'total_income'   => $totalIncome,
                'total_expense'  => $totalExpense,
                'surplus_defisit'=> $surplusDeficit,
                'status' => $surplusDeficit >= 0 ? 'Surplus' : 'Defisit',
            ],
        ]);
    }
}
