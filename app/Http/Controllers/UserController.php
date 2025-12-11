<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function update(Request $request, $id)
    {
        $request->validate([
            'photo' => 'nullable|string',
            'preference' => 'nullable|string',
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'photo' => $request->photo,
            'preference' => $request->preference,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User updated',
            'data' => $user
        ]);
    }
}
