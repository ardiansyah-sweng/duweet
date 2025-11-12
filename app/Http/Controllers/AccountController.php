<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Ambil seluruh struktur akun secara nested (parent → children → subchildren)
     */
    public function index()
    {
        // Ambil akun root (tidak punya parent)
        $accounts = Account::with('childrenRecursive')
            ->whereNull('parent_id')
            ->orderBy('code')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data akun berhasil diambil',
            'data' => $accounts
        ]);
    }

    /**
     * Ambil struktur akun berdasarkan ID parent tertentu
     */
    public function show($id)
    {
        $account = Account::with('childrenRecursive')->find($id);

        if (!$account) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data akun berhasil diambil',
            'data' => $account
        ]);
    }
}
