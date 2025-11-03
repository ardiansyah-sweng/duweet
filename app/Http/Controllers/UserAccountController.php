<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\UserAccount;

class UserAccountController extends Controller
{
    // Fungsi untuk query insert
    public function store(Request $request)
    {

        $validated = $request->validate([
            'user_id' => 'required|integer',
            'username' => 'required|string|unique:user_accounts,username',
            'email' => 'required|email|unique:user_accounts,email',
            'password' => 'required|string|min:6',
        ]);
        
        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now();
        $validated['is_active'] = true;

        // Simpan ke database menggunakan model
        $userAccount = UserAccount::create($validated);

        return response()->json([
            'message' => 'UserAccount berhasil ditambahkan!',
            'data' => $request->only(['username', 'email'])
        ]);
    }
}
