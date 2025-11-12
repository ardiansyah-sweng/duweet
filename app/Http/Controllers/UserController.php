<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Constants\UserColumns;
use App\Constants\UserAccountColumns;

class UserController extends Controller
{
    /**
     * Ambil semua account yang dimiliki user via UserAccount
     * Output JSON dengan kolom ID User, Username, Email, Verified, Is Active
     */
    public function getAllAccounts($id)
    {
        // Ambil user langsung dari tabel menggunakan db_tables.php
        $userTable = config('db_tables.user');
        $accountTable = config('db_tables.user_account');

        $user = DB::table($userTable)
            ->where(UserColumns::ID, $id)
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        // Ambil semua akun user dari tabel user_accounts
        $accounts = DB::table($accountTable)
            ->where(UserAccountColumns::ID_USER, $id)
            ->get();

        // Map data ke format JSON yang rapi
        $data = $accounts->map(function($account) {
            return [
                'ID User'   => $account->{UserAccountColumns::ID_USER},
                'Username'  => $account->{UserAccountColumns::USERNAME},
                'Email'     => $account->{UserAccountColumns::EMAIL},
                'Verified'  => $account->{UserAccountColumns::VERIFIED_AT},
                'Is Active' => $account->{UserAccountColumns::IS_ACTIVE} ? 'Yes' : 'No',
            ];
        });

        // Kembalikan JSON
        return response()->json([
            'User ID' => $user->{UserColumns::ID},
            'Name'    => $user->{UserColumns::NAME},
            'Accounts'=> $data
        ], 200);
    }
}
