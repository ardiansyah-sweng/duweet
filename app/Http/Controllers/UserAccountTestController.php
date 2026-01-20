<?php

namespace App\Http\Controllers;

use App\Models\UserAccount;
use Illuminate\Http\Request;

class UserAccountTestController extends Controller
{
    public function testLogin(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'login' => 'required|string', // Bisa email atau username
            'password' => 'required|string'
        ]);

        $login = $request->login;
        $password = $request->password;

        /**
         * 2. Cari User menggunakan fungsi BARU yang kita buat di Model
         * Kita coba cari berdasarkan email login dulu
         */
        $user = UserAccount::cariUserByEmailLogin($login, $password);

        // 3. Jika berdasarkan email tidak ketemu, coba cari berdasarkan username login
        if (!$user) {
            $user = UserAccount::cariUserByUsernameLogin($login, $password);
        }

        // 4. Response JSON untuk Postman
        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'id' => $user->id ?? null,
                    'username' => $user->username,
                    'email' => $user->email,
                    'is_active' => (bool) ($user->is_active ?? false),
                ]
            ]);
        } else {
            // Karena kita menggunakan password langsung di Query SQL, 
            // jika tidak ketemu artinya kemungkinan email/username salah ATAU password salah
            return response()->json([
                'success' => false,
                'message' => 'Login gagal: Email/Username atau Password salah'
            ], 401);
        }
    }
}