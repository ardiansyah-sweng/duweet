<?php

namespace App\Http\Controllers;

use App\Models\UserAccount;
use Illuminate\Http\Request;

class UserAccountTestController extends Controller
{
    /**
     * Test login berdasarkan email/username & password
     * Fokus: Menggunakan method yang benar dari Model UserAccount.php
     */
    public function testLogin(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'login' => 'required|string', // Bisa diisi email atau username
            'password' => 'required|string'
        ]);

        $login = $request->login;
        $password = $request->password;

        /**
         * 2. Cari User
         * Perbaikan: Menggunakan 'cariUserByEmail' karena itu yang tersedia di Model kamu.
         * Jika kamu ingin mencari berdasarkan username juga, pastikan fungsi 'cariUserByUsername' 
         * sudah ditambahkan di Model UserAccount.php.
         */
        $user = UserAccount::cariUserByEmail($login);

        // Jika pencarian email gagal, kita coba asumsikan input tersebut adalah username
        if (!$user) {
            // Catatan: Pastikan kamu sudah menambahkan method cariUserByUsername di model UserAccount
            $query = \Illuminate\Support\Facades\DB::select("SELECT * FROM user_accounts WHERE username = ? LIMIT 1", [$login]);
            $user = $query[0] ?? null;
        }

        // 3. Cek apakah user ditemukan
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ]);
        }

        /**
         * 4. Verifikasi Password
         * Fokus: Mencocokkan teks asli dari Postman dengan hash di database.
         */
        if (password_verify($password, $user->password)) {
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
            return response()->json([
                'success' => false,
                'message' => 'Password salah'
            ]);
        }
    }
}