<?php

namespace App\Http\Controllers;

use App\Constants\UserAccountColumns;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserAccountController extends Controller
{
    public function updatePassword(Request $request, $id)
{
    $request->validate([
        'password' => 'required|min:8'
    ]);

    $result = UserAccount::updatePasswordById($id, $request->password);

    return response()->json([
        'success' => (bool) $result,
        'id' => $id
    ]);
}
}
