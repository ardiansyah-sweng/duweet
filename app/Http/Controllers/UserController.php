<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of all users
     */
    public function index()
    {
        $users = User::all();
        
        return response()->json([
            'success' => true,
            'data' => $users,
            'total' => $users->count()
        ]);
    }

    /**
     * Search users berdasarkan nama, email, atau alamat
     * 
     * Query Parameters:
     * - q (required): keyword pencarian
     * 
     * Endpoint: GET /api/user/search?q=keyword
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        // Validasi input
        if (!$query || trim($query) === '') {
            return response()->json([
                'success' => false,
                'message' => 'Parameter pencarian (q) tidak boleh kosong'
            ], 400);
        }

        // Pencarian berdasarkan nama, email, dan alamat
        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhere('jalan', 'like', "%{$query}%")
            ->orWhere('kabupaten', 'like', "%{$query}%")
            ->orWhere('kecamatan', 'like', "%{$query}%")
            ->orWhere('provinsi', 'like', "%{$query}%")
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users,
            'total' => $users->count()
        ]);
    }

    /**
     * Display a specific user by ID
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Store a new user (Registrasi)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'jalan' => 'nullable|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
            'tanggal_lahir' => 'nullable|integer|between:1,31',
            'bulan_lahir' => 'nullable|integer|between:1,12',
            'tahun_lahir' => 'nullable|integer',
            'usia' => 'nullable|integer|min:0',
        ]);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dibuat',
            'data' => $user
        ], 201);
    }

    /**
     * Update a user
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id . '|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'jalan' => 'nullable|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
            'tanggal_lahir' => 'nullable|integer|between:1,31',
            'bulan_lahir' => 'nullable|integer|between:1,12',
            'tahun_lahir' => 'nullable|integer',
            'usia' => 'nullable|integer|min:0',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diupdate',
            'data' => $user
        ]);
    }

    /**
     * Delete a user
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus'
        ]);
    }
}
