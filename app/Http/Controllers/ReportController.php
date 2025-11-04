<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserFinancialAccount;

class ReportController extends Controller
{
    /**
     * Menampilkan semua user yang belum memiliki akun finansial.
     */
    public function usersWithoutAccounts()
    {
        // Ambil data user tanpa akun finansial (pakai scope dari User model)
        $users = User::withoutAccounts()
            ->select('id', 'name', 'email', 'usia', 'bulan_lahir', 'tanggal_lahir', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        // Jika tidak ada user ditemukan
        if ($users->isEmpty()) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Tidak ada user yang belum memiliki akun finansial.',
                'total_users' => 0,
                'data' => []
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar user yang belum memiliki akun finansial.',
            'total_users' => $users->count(),
            'data' => $users
        ], 200);
    }

    /**
     * Menampilkan semua user yang tidak punya akun aktif.
     */
    public function usersWithoutActiveAccounts()
    {
        // Ambil user yang tidak punya akun aktif
        $users = User::withoutActiveAccounts()
            ->select('id', 'name', 'email', 'usia', 'bulan_lahir', 'tanggal_lahir', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($users->isEmpty()) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Tidak ada user yang tidak memiliki akun aktif.',
                'total_users' => 0,
                'data' => []
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar user yang tidak memiliki akun aktif.',
            'total_users' => $users->count(),
            'data' => $users
        ], 200);
    }

    /**
     * Menampilkan semua aset likuid milik user berdasarkan ID.
     */
    public function userLiquidAsset($id)
    {
        // Query akun finansial user berdasarkan ID user
        $assets = UserFinancialAccount::where('user_id', $id)
            ->with(['financialAccount' => function ($query) {
                $query->select('id', 'name', 'type', 'balance');
            }])
            ->get(['id', 'user_id', 'financial_account_id', 'balance', 'is_active', 'created_at']);

        if ($assets->isEmpty()) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'User tidak memiliki akun finansial atau data aset tidak ditemukan.',
                'total_assets' => 0,
                'data' => []
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar aset likuid milik user.',
            'user_id' => (int) $id,
            'total_assets' => $assets->count(),
            'data' => $assets
        ], 200);
    }
}
