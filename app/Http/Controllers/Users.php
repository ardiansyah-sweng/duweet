<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Users_account;
use App\Models\User;

class Users extends Controller
{
    public function index()
    {
        try {
            $data = Users_account::with('user')
                ->where('is_active', true)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $data,
                'message' => 'Data berhasil diambil'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }
}