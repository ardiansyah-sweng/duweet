<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserNestedController extends Controller
{
    // Menampilkan struktur nested User dan Account dalam bentuk tampilan (bukan JSON)
    public function index()
    {
        $data = User::with('userAccount.children.parent')->get();
        return view('nested', compact('data'));
    }
}
