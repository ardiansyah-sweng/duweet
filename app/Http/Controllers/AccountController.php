<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function totalPerUser()
    {
        $results = DB::select("
            SELECT 
                u.id AS user_id,
                u.name AS user_name,
                SUM(uat.total_balance) AS total_balance
            FROM user_account_totals AS uat
            JOIN users AS u ON uat.user_id = u.id
            JOIN accounts AS a ON uat.account_id = a.id
            GROUP BY u.id, u.name
            ORDER BY total_balance DESC
        ");

        // Kirim hasil ke tampilan (view)
        return view('totals', ['results' => $results]);
    }
}

