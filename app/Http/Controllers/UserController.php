<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Constants\UserColumns;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user(); // ambil user yang sedang login

        $validated = $request->validate([
            UserColumns::NAME => 'required|string|max:255',
            UserColumns::EMAIL => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            UserColumns::PHOTO => 'nullable|image|max:2048',
            UserColumns::PREFERENCE => 'nullable|array',
        ]);

        // Update kolom dasar
        $user->name = $validated[UserColumns::NAME];
        $user->email = $validated[UserColumns::EMAIL];

        // Update password (jika diisi)
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Upload foto
        if ($request->hasFile(UserColumns::PHOTO)) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }

            $path = $request->file(UserColumns::PHOTO)->store('avatars', 'public');
            $user->photo = $path;
        }

        // Update preference (jika dikirim)
        if (isset($validated[UserColumns::PREFERENCE])) {
            $user->preference = $validated[UserColumns::PREFERENCE];
        }

        $user->save();

        return response()->json([
            'message' => 'Data pengguna berhasil diperbarui!',
            'user' => $user
        ]);
    }
}
