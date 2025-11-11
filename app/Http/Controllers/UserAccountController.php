<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;     
use Illuminate\Support\Facades\Hash;
use App\Constants\UserAccountColumns;

class UserAccountController extends Controller
{
    // Fungsi untuk query insert
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            UserAccountColumns::ID_USER     => 'required|integer',
            UserAccountColumns::USERNAME    => 'required|string|unique:user_accounts,' . UserAccountColumns::USERNAME,
            UserAccountColumns::EMAIL       => 'required|email|unique:user_accounts,' . UserAccountColumns::EMAIL,
            UserAccountColumns::PASSWORD    => 'required|string|min:6',
        ]);


        $validated[UserAccountColumns::PASSWORD] = Hash::make($validated[UserAccountColumns::PASSWORD]);
        

        $validated[UserAccountColumns::VERIFIED_AT] = now();
        $validated[UserAccountColumns::IS_ACTIVE]   = true;

        DB::table('user_accounts')->insert($validated);

        return response()->json([
            'Notifikasi' => 'UserAccount berhasil ditambahkan!',
            'data' => [
                UserAccountColumns::USERNAME => $validated[UserAccountColumns::USERNAME],
                UserAccountColumns::EMAIL    => $validated[UserAccountColumns::EMAIL],
            ],
        ], 201);
    }
}
