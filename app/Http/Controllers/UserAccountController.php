<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAccount;


class UserAccountController extends Controller
{
    public function tidakLogin(Request $request)
    {
        // ambil parameter dari query string, misalnya ?mulai=...&selesai=...
        $mulai = $request->query('mulai', '2025-10-01');
        $selesai = $request->query('selesai', '2025-10-31');

        $data = UserAccount::query_user_yang_tidak_login_dalam_periode_tertentu($mulai, $selesai);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
}