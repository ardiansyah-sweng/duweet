<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

// Models
use App\Models\User;
use App\Models\UserAccount;
use App\Models\FinancialAccount;
use App\Models\UserFinancialAccount;
use App\Models\Transaction;
use App\Models\Cashout;

// Constants
use App\Constants\UserColumns;
use App\Constants\AccountColumns;
use App\Constants\FinancialAccountColumns;
use App\Constants\UserFinancialAccountColumns;
use App\Constants\TransactionColumns;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']); // Middleware admin
    }

    /* =======================================================
     * BAGIAN 1 — REPORT TRANSACTION (DARI FILE PERTAMA)
     * ======================================================= */

    public function incomeSummary(Request $request)
    {
        $baseData = $this->getReportBaseData($request);

        if ($baseData instanceof \Illuminate\Http\JsonResponse) {
            return $baseData;
        }

        ['user' => $user, 'userAccount' => $userAccount, 'userData' => $userData, 'userAccountData' => $userAccountData] = $baseData;

        $startDate = $request->start_date
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::create(2025, 1, 1)->startOfDay();

        $endDate = $request->end_date
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::create(2025, 12, 31)->endOfDay();

        if ($startDate->greaterThan($endDate)) {
            return response()->json(['error' => 'Tanggal mulai lebih besar dari tanggal akhir'], 400);
        }

        try {
            $summary = Transaction::getIncomeSummaryByPeriod(
                $userAccount->id,
                $startDate,
                $endDate
            );

            return response()->json([
                'user' => $userData,
                'user_account' => $userAccountData,
                'summary' => $summary,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil ringkasan transaksi.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function getReportBaseData(Request $request)
    {
        $email = $request->query('email');
        $userId = $request->query('user_id');
        $userAccountId = $request->query('user_account_id');

        if ($userAccountId) {
            $userAccount = DB::table('user_accounts')->where('id', $userAccountId)->first();
            if (!$userAccount) {
                return response()->json(['error' => 'User account not found'], 404);
            }

            $user = User::find($userAccount->id_user);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
        } else {
            if ($email) {
                $user = User::where('email', $email)->first();
            } elseif ($userId) {
                $user = User::find($userId);
            } else {
                $user = User::first();
            }
        }

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $userAccount ??= DB::table('user_accounts')->where('id_user', $user->id)->first();

        if (!$userAccount) {
            $userAccount = DB::table('user_accounts')->first();
            $user = User::find($userAccount->id_user);
        }

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'provinsi' => $user->provinsi,
            'kabupaten' => $user->kabupaten,
        ];

        $userAccountData = [
            'id' => $userAccount->id,
            'username' => $userAccount->username,
            'email' => $userAccount->email,
        ];

        return compact('user', 'userAccount', 'userData', 'userAccountData');
    }

    public function getTotalTransactionsPerUserAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_account_id' => 'nullable|integer|exists:user_accounts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userAccountId = $request->query('user_account_id');
        $data = Transaction::getTotalTransactionsPerUserAccount($userAccountId);

        return response()->json([
            'status' => 'success',
            'filter' => ['user_account_id' => $userAccountId],
            'count' => $data->count(),
            'data' => $data,
        ]);
    }

    public function adminSpendingSummary(Request $request)
    {
        $startDate = $request->start_date
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->end_date
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfMonth();

        if ($startDate->greaterThan($endDate)) {
            return response()->json(['success' => false, 'message' => 'Tanggal awal > tanggal akhir'], 400);
        }

        try {
            $data = Transaction::getTotalSpendingByUserAccountAdmin($startDate, $endDate);

            return response()->json([
                'success' => true,
                'period' => [
                    'from' => $startDate->toDateString(),
                    'to' => $endDate->toDateString(),
                ],
                'total_user_accounts' => $data->count(),
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function sumFinancialAccountsByType()
    {
        return response()->json(
            UserFinancialAccount::sumAllUsersFinancialAccountsByType()
        );
    }

    public function surplusDefisitByPeriod(Request $request)
    {
        $baseData = $this->getReportBaseData($request);

        if ($baseData instanceof \Illuminate\Http\JsonResponse) {
            return $baseData;
        }

        ['user' => $user, 'userAccount' => $userAccount, 'userData' => $userData, 'userAccountData' => $userAccountData] = $baseData;

        $startDate = $request->start_date
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->end_date
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfMonth();

        $summary = Transaction::getSurplusDefisitByPeriod(
            $userAccount->id,
            $startDate,
            $endDate
        );

        return response()->json([
            'user' => $userData,
            'user_account' => $userAccountData,
            'period' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
            'summary' => $summary
        ]);
    }


    /* =======================================================
     * BAGIAN 2 — REPORT CASHOUT (DARI FILE KEDUA)
     * ======================================================= */

    public function showCashoutSumForm()
    {
        return view('admin.reports.cashout_sum_form');
    }

    public function getCashoutSumByPeriod(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $start = $request->start_date;
        $end   = $request->end_date;

        $total = Cashout::betweenDates($start, $end)->sum('amount');

        $breakdown = Cashout::select(
                DB::raw('DATE(created_at) AS date'),
                DB::raw('SUM(amount) AS total_amount'),
                DB::raw('COUNT(*) AS count_tx')
            )
            ->betweenDates($start, $end)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        return view('admin.reports.cashout_sum_result', compact(
            'start', 'end', 'total', 'breakdown'
        ));
    }

    public function exportCashoutCsv(Request $request)
    {
        $rows = Cashout::betweenDates($request->start_date, $request->end_date)
            ->orderBy('created_at')
            ->get();

        $filename = "cashout_{$request->start_date}_{$request->end_date}.csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () use ($rows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'User ID', 'Amount', 'Status', 'Created At']);

            foreach ($rows as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->user_id,
                    $row->amount,
                    $row->status,
                    $row->created_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
