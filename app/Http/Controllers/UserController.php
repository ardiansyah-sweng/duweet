<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Terima request, validasi, dan delegasikan insert ke model (createUserRaw).
     */
    public function createUserRaw(Request $request): JsonResponse
    {
        // Validasi input dasar di controller — biar model tetap bertanggung jawab atas query
        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'sometimes|string|max:255',
            'first_name' => 'sometimes|string|max:255',
            'middle_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'provinsi' => 'sometimes|string|max:255',
            'kabupaten' => 'sometimes|string|max:255',
            'kecamatan' => 'sometimes|string|max:255',
            'jalan' => 'sometimes|string',
            'kode_pos' => 'sometimes|string|max:20',
            'tanggal_lahir' => 'sometimes|date',
            'bulan_lahir' => 'sometimes|integer|min:1|max:12',
            'tahun_lahir' => 'sometimes|integer|min:1900|max:' . date('Y'),
            'usia' => 'sometimes|integer|min:0',
            'telephones' => 'sometimes|array',
            'telephones.*' => 'string',
        ]);

        $result = User::createUserRaw($validated);

        if (is_string($result)) {
            $status = 400;
            if (str_contains(strtolower($result), 'email sudah digunakan') ||
                str_contains($result, '1062') ||
                str_contains(strtolower($result), 'duplicate')) {
                $status = 200; 
            }

            return response()->json([
                'success' => true,
                'message' => $result
            ], $status);
        }

        // Jika model mengembalikan id (sukses)
        $userId = (int) $result;
        $user = User::with('telephones')->find($userId);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dibuat.',
            'data' => [
                'user_id' => $userId,
                'user' => $user
            ]
        ], 201);
    }
}
