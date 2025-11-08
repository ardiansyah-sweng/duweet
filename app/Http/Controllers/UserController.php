<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Create user using raw query
     */
    public function createUserRaw(Request $request): JsonResponse
    {
        // Validasi request
        $request->validate([
            'email' => 'required|email',
            'name' => 'sometimes|string|max:255',
            'first_name' => 'sometimes|string|max:255',
            'middle_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'provinsi' => 'sometimes|string|max:255',
            'kabupaten' => 'sometimes|string|max:255',
            'kecamatan' => 'sometimes|string|max:255',
            'jalan' => 'sometimes|string',
            'kode_pos' => 'sometimes|string|max:10',
            'tanggal_lahir' => 'sometimes|date',
            'bulan_lahir' => 'sometimes|integer|min:1|max:12',
            'tahun_lahir' => 'sometimes|integer|min:1900|max:' . date('Y'),
            'telephones' => 'sometimes|array',
            'telephones.*' => 'string',
        ]);

        try {
            $result = User::createUserRaw($request->all());

            if (is_string($result)) {
                // Jika return string, berarti error
                return response()->json([
                    'success' => false,
                    'message' => $result
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dibuat',
                'data' => [
                    'user_id' => $result,
                    'user' => User::with('telephones')->find($result)
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }
}