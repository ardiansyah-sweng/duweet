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
        $userTable = config('db_tables.user');
        $accountTable = config('db_tables.user_account');

        // Cek user
        $user = DB::table($userTable)
            ->where(UserColumns::ID, $id)
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        // Ambil semua akun milik user tersebut
        $accounts = DB::table($accountTable)
            ->select(
                UserAccountColumns::ID_USER,
                UserAccountColumns::USERNAME,
                UserAccountColumns::EMAIL,
                UserAccountColumns::VERIFIED_AT,
                UserAccountColumns::IS_ACTIVE
            )
            ->where(UserAccountColumns::ID_USER, $id)
            ->get();

        // Format JSON
        $formattedAccounts = $accounts->map(function ($acc) {
            return [
                'ID User'   => $acc->{UserAccountColumns::ID_USER},
                'Username'  => $acc->{UserAccountColumns::USERNAME},
                'Email'     => $acc->{UserAccountColumns::EMAIL},
                'Verified'  => $acc->{UserAccountColumns::VERIFIED_AT},
                'Is Active' => $acc->{UserAccountColumns::IS_ACTIVE} ? 'Yes' : 'No',
            ];
        });

        return response()->json([
            'User ID'  => $user->{UserColumns::ID},
            'Name'     => $user->{UserColumns::NAME},
            'Accounts' => $formattedAccounts
        ], 200);
    }
}
