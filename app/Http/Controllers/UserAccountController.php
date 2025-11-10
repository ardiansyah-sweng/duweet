<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\UserAccount;
use App\Constants\UserAccountColumns;

class UserAccountController extends Controller
{
    // Fungsi untuk query insert
    public function store(Request $request)
    {
        $validated = $request->validate([
            UserAccountColumns::ID_USER     => 'required|integer',
            UserAccountColumns::USERNAME    => 'required|string|unique:user_accounts,' . UserAccountColumns::USERNAME,
            UserAccountColumns::EMAIL       => 'required|email|unique:user_accounts,' . UserAccountColumns::EMAIL,
            UserAccountColumns::PASSWORD    => 'required|string|min:6',
        ]);

        // Hash password
        $validated[UserAccountColumns::PASSWORD] = Hash::make($validated[UserAccountColumns::PASSWORD]);
        
        // Tambahan kolom lain
        $validated[UserAccountColumns::VERIFIED_AT] = now();
        $validated[UserAccountColumns::IS_ACTIVE]   = true;

        // Simpan ke database menggunakan model
        $userAccount = UserAccount::create($validated);

        return response()->json([
            'message' => 'UserAccount berhasil ditambahkan!',
            'data' => [
                UserAccountColumns::USERNAME => $userAccount->{UserAccountColumns::USERNAME},
                UserAccountColumns::EMAIL    => $userAccount->{UserAccountColumns::EMAIL},
            ],
        ]);
    }
}
