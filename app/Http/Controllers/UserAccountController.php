<?php

namespace App\Http\Controllers;

use App\Constants\UserAccountColumns;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserAccountController extends Controller
{
    public function updatePassword($id, Request $request)
    {
        
        $password = $request->input('password');

        $result = UserAccount::updatePasswordById($id, $password);

        return response()->json([
            'success' => $result ? true : false,
            'id_user' => $id,
            'password_baru' => $password
        ]);
    }
}
