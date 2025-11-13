<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
{
    // Ambil keyword pencarian dari input
    $search = $request->input('search');

    // Ambil sorting (default sort by created_at)
    $sort = $request->input('sort', 'created_at');

    // Query pencarian + sorting
    $users = User::query()
        ->when($search, function($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        })
        ->orderBy(
            str_replace('-', '', $sort),        // ambil nama kolom tanpa tanda -
            $sort[0] === '-' ? 'desc' : 'asc'   // descending jika ada tanda '-'
        )
        ->paginate(10);

    return view('admin.users.index', compact('users', 'search', 'sort'));
}

}
