<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user dengan fitur pencarian.
     */
    public function index(Request $request)
    {
        $q = $request->query('q'); // keyword pencarian
        $sort = $request->query('sort', '-created_at'); // urutan default terbaru
        $perPage = intval($request->query('per_page', 10)); // jumlah data per halaman

        $query = User::query();

        // 🔍 Filter berdasarkan nama/email/username
        if ($q) {
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('username', 'like', "%{$q}%");
            });
        }

        // 🔄 Sorting data
        if (str_starts_with($sort, '-')) {
            $column = substr($sort, 1);
            $query->orderBy($column, 'desc');
        } else {
            $query->orderBy($sort, 'asc');
        }

        // 📄 Pagination
        $users = $query->paginate($perPage)->withQueryString();

        return view('admin.users.index', compact('users', 'q', 'sort'));
    }
}
