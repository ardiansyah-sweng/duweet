<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Users extends Controller
{
    public function index()
    {
        $data = DB::table('user_accounts')->get();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);
    }
}
