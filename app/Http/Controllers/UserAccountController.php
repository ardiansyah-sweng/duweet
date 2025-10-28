<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserAccountController extends Controller
{
    // Fungsi untuk query insert
    public function store(Request $request)
    {

        $request->validate([
            'user_id' => 'required|integer',
            'username' => 'required|string|unique:user_accounts,username',
            'email' => 'required|email|unique:user_accounts,email',
            'password' => 'required|string|min:6',
        ]);

        DB::table('user_accounts')->insert([
            'user_id' => $request->user_id,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'UserAccount berhasil ditambahkan!',
            'data' => $request->only(['username', 'email'])
        ]);
    }
}
