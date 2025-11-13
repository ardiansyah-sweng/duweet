<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class LoginController extends BaseController
{
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $email)->first();

        if ($user && Hash::check($password, $user->password)) {
            
            Auth::login($user); 
            
            return response()->json([
                'status' => 'success',
                'message' => 'Login Berhasil!',
                'redirect' => '/dashboard' 
            ], 200);

        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau Password yang Anda masukkan salah.'
            ], 401);
        }
    }
}
