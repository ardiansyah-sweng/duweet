<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\UserAccount;
use Illuminate\Http\JsonResponse;

class UserAccountController extends Controller
{
    /**
     * Reset a user's password by email.
     *
     * Expected JSON body: { "email": "user@example.com", "new_password": "newpass" }
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'new_password' => ['required', 'string', 'min:6'],
        ]);

        $updated = UserAccount::resetPasswordByEmail($data['email'], $data['new_password']);

        $user = UserAccount::cariUserByEmail($data['email']);

        // WARNING: returning plain passwords is insecure. This is intended
        // for local testing only per your request.
        return response()->json([
            'updated' => $updated,
            'new_password' => $data['new_password'],
            'user' => $user,
        ]);
    }

    /**
     * Find a user account by email (GET) for reset-password flow.
     * Example: GET /api/user/find?email=resetme@example.com
     */
    public function findByEmail(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = UserAccount::cariUserByEmail($data['email']);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Remove sensitive fields before returning
        $safe = [
            'id' => $user->id ?? null,
            'id_user' => $user->id_user ?? null,
            'username' => $user->username ?? null,
            'email' => $user->email ?? null,
            'verified_at' => $user->verified_at ?? null,
            'is_active' => $user->is_active ?? null,
            'created_at' => $user->created_at ?? null,
            'updated_at' => $user->updated_at ?? null,
        ];

        return response()->json(['user' => $safe]);
    }

    /**
     * Reset password via GET (for quick testing only).
     * Example: GET /api/user/reset-password?email=foo@ex.com&new_password=abc123
     * WARNING: Sending passwords via GET is insecure (appears in logs, browser history).
     */
    public function resetPasswordViaGet(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'new_password' => ['required', 'string', 'min:6'],
        ]);

        $user = UserAccount::cariUserByEmail($data['email']);
        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $updated = UserAccount::resetPasswordByEmail($data['email'], $data['new_password']);

        $userAfter = UserAccount::cariUserByEmail($data['email']);

        // Return safe user (no password)
        $safe = [
            'id' => $userAfter->id ?? null,
            'id_user' => $userAfter->id_user ?? null,
            'username' => $userAfter->username ?? null,
            'email' => $userAfter->email ?? null,
            'verified_at' => $userAfter->verified_at ?? null,
            'is_active' => $userAfter->is_active ?? null,
            'created_at' => $userAfter->created_at ?? null,
            'updated_at' => $userAfter->updated_at ?? null,
        ];

        return response()->json([
            'updated' => $updated,
            // WARNING: returning plain passwords is insecure. This is intended
            // for local testing only per your request.
            'new_password' => $data['new_password'],
            'user' => $safe,
        ]);
    }
}
