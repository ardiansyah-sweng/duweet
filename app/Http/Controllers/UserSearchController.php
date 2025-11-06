<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserSearchController extends Controller
{
  public function search(Request $request)
{
    $filters = $request->only([
        'q','name','username','email','start','end','sort_by','sort_dir','per_page'
    ]);
    $perPage = (int)($filters['per_page'] ?? 20);

    $rows = User::searchUsers($filters)->paginate($perPage);

    return response()->json([
        'success' => true,
        'data' => $rows->items(),
        'meta' => [
            'current_page' => $rows->currentPage(),
            'per_page'     => $rows->perPage(),
            'total'        => $rows->total(),
            'last_page'    => $rows->lastPage(),
        ],
    ]);
}
}