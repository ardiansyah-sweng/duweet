<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Menghapus user (hard delete) beserta relasi cascade-nya.
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        // Hard delete user (cascade akan berjalan otomatis)
        $user->delete();

        return response()->json([
            'message' => 'User dan semua data terkait telah dihapus permanen (cascade).'
        ]);
    }
}
