<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    /**
     * Tampilkan semua akun user berdasarkan user ID
     */
    public function showAccounts($id)
    {
        $user = User::find($id);

        if (!$user) {
            return "User dengan ID {$id} tidak ditemukan.";
        }

        $accounts = $user->getAllAccounts();

        $output = "";
        foreach ($accounts as $account) {
            $output .= $account->username . " - " . $account->email . "<br>";
        }

        return $output;
    }
}
