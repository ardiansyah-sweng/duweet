<?php

namespace App\Http\Controllers;

use App\Models\UserAccount; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Validation\Rule; 

class AccountController extends Controller
{
    // ... method index/store lainnya ...

    // METHOD UPDATE USER ACCOUNT (Raw Query Implementation)
    public function update($id, Request $request)
    {
        try {
            // 1. Cek apakah User Account ada (Pencegahan Error Not Found)
            // Menggunakan helper Raw Query dari Model
            $existingUser = UserAccount::findRaw($id);
            
            if (!$existingUser) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "User Account dengan ID {$id} tidak ditemukan",
                ], 404);
            }

            // 2. Validasi Input
            // Rule::unique mengabaikan ID saat ini agar tidak error "email has already been taken" jika tidak diganti
            $validated = $request->validate([
                'username'  => ['sometimes', 'string', 'max:255', Rule::unique('user_accounts')->ignore($id)],
                'email'     => ['sometimes', 'email', 'max:255', Rule::unique('user_accounts')->ignore($id)],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            // Jika tidak ada data yang dikirim di body request
            if (empty($validated)) {
                return response()->json([
                    'status'  => 'warning',
                    'message' => 'Tidak ada data yang dikirim untuk diupdate',
                    'data'    => $existingUser
                ], 400);
            }

            // 3. Eksekusi Update via Model (Raw Query)
            $updateSuccess = UserAccount::updateRaw($id, $validated);

            if (!$updateSuccess) {
                // Jika return 0, bisa jadi karena data sama persis dengan di DB
                return response()->json([
                    'status'  => 'success', 
                    'message' => 'Data tidak berubah (isi data sama dengan sebelumnya)',
                    'id'      => $id
                ], 200);
            }

            // 4. Ambil data terbaru untuk respon (Fresh Data)
            $updatedUser = UserAccount::findRaw($id);

            return response()->json([
                'status'  => 'success',
                'message' => 'User Account berhasil diupdate',
                'id'      => $id,
                'data'    => $updatedUser
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'validation_error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage(),
            ], 500);
        }
    }
}