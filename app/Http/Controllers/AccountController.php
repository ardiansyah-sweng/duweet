<?php

namespace App\Http\Controllers;

use App\Models\UserFinancialAccount;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = UserFinancialAccount::with('childrenRecursive')->get();
        return response()->json($accounts);
    }
}
