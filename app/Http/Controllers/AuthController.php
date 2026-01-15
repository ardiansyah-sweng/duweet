<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function ProsesLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
        ], [
            'username.required' => 'Username harus diisi.',
        ]);

        try {
            
            $query = "SELECT 
                        ua.id as user_account_id,
                        ua.id_user,
                        ua.username,
                        ua.email,
                        ua.is_active,
                        ua.verified_at,
                        u.name as user_name
                      FROM user_accounts ua
                      INNER JOIN users u ON ua.id_user = u.id
                      WHERE ua.username = ?
                      LIMIT 1";

            $userAccount = DB::selectOne($query, [$request->username]);

            if (!$userAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Username tidak ditemukan.'
                ], 401);
            }

            if (!$userAccount->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun tidak aktif. Silakan hubungi administrator.'
                ], 403);
            }

            $token = hash('sha256', Str::random(60) . time() . $userAccount->user_account_id);

            $expiresAt = now()->addHours(24);
            
            Log::info('Token Created', [
                'token' => $token,
                'user_account_id' => $userAccount->user_account_id,
                'expires_at' => $expiresAt,
            ]);

            Cache::put('auth_token_' . $token, [
                'user_account_id' => $userAccount->user_account_id,
                'id_user' => $userAccount->id_user,
                'username' => $userAccount->username,
                'email' => $userAccount->email,
            ], $expiresAt);

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil.',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'expires_at' => $expiresAt->toDateTimeString(),
                    'user' => [
                        'user_account_id' => $userAccount->user_account_id,
                        'user_id' => $userAccount->id_user,
                        'username' => $userAccount->username,
                        'email' => $userAccount->email,
                        'name' => $userAccount->user_name,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat login: ' . $e->getMessage()
            ], 500);
        }
    }

}
