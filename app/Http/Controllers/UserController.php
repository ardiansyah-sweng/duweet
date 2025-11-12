<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Constants\UserAccountColumns;

class UserController extends Controller
{
    public function getAllAccounts($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $accounts = $user->userAccounts()->get();

        $data = $accounts->map(function($account) {
            return [
                'ID User'   => $account->{UserAccountColumns::ID_USER},
                'Username'  => $account->{UserAccountColumns::USERNAME},
                'Email'     => $account->{UserAccountColumns::EMAIL},
                'Verified'  => $account->{UserAccountColumns::VERIFIED_AT},
                'Is Active' => $account->{UserAccountColumns::IS_ACTIVE} ? 'Yes' : 'No',
            ];
        });

        return response()->json([
            'User ID' => $user->id,
            'Name'    => $user->name,
            'Accounts'=> $data
        ], 200);
    }
}
