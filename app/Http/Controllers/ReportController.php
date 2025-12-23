<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Constants\TransactionColumns;
use App\Constants\UserAccountColumns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Routing\Controller;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Hitung surplus/defisit user berdasarkan ID atau user_account_id dan periode.
     *
     * GET /api/report/surplus-deficit/{userId}
     * Query Parameters:
     *  - start_date (Y-m-d) wajib
     *  - end_date (Y-m-d) wajib
     *  - user_account_id (opsional, pakai akun tertentu)
     */
    public function getUserSurplusDeficit(Request $request, int $userId)
    {
        // 1. Validasi input tanggal
        try {
            $request->validate([
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
                'user_account_id' => 'nullable|integer|exists:user_accounts,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $startDateString = $request->start_date;
        $endDateString = $request->end_date;

        // 2. Cek user
        $user = User::select('id', 'name', 'email')->find($userId);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        // 3. Ambil user_account
        $userAccountId = $request->input('user_account_id');
        if ($userAccountId) {
            $userAccount = DB::table('user_accounts')
                ->where('id', $userAccountId)
                ->where('id_user', $userId)
                ->first();
            if (!$userAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'User account not found for this user.'
                ], 404);
            }
        } else {
            $userAccount = DB::table('user_accounts')
                ->where('id_user', $userId)
                ->first();
            if (!$userAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'No user accounts found for this user.'
                ], 404);
            }
            $userAccountId = $userAccount->id;
        }

        // 4. Query transaksi
        $report = DB::table('transactions as t')
            ->join('user_accounts as ua', 't.user_account_id', '=', 'ua.id')
            ->where('ua.id', $userAccountId)
            ->whereBetween('t.' . TransactionColumns::CREATED_AT, [$startDate, $endDate])
            ->selectRaw("
                SUM(CASE WHEN t." . TransactionColumns::ENTRY_TYPE . " = 'credit' THEN t." . TransactionColumns::AMOUNT . " ELSE 0 END) AS total_income,
                SUM(CASE WHEN t." . TransactionColumns::ENTRY_TYPE . " = 'debit' THEN t." . TransactionColumns::AMOUNT . " ELSE 0 END) AS total_expense
            ")
            ->first();

        $totalIncome = (float) ($report->total_income ?? 0);
        $totalExpense = (float) ($report->total_expense ?? 0);
        $surplusDeficit = $totalIncome - $totalExpense;
        $status = $surplusDeficit >= 0 ? 'Surplus' : 'Defisit';

        // 5. Response JSON
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'user_account' => [
                'id' => $userAccount->id,
                'username' => $userAccount->username ?? null,
                'email' => $userAccount->email ?? null,
            ],
            'period' => "Dari {$startDateString} sampai {$endDateString}",
            'summary' => [
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'surplus_defisit' => $surplusDeficit,
                'status' => $status,
            ]
        ]);
    }
}
