<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserNestedController extends Controller
{
    public function index()
    {
        $users = User::with('userFinancialAccounts')->get();
        return view('nested', compact('users'));
    }
}
