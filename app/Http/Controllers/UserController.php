<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Contracts\View\Factory;

class UserController extends Controller
{
    /**
     * Display a listing of users with search functionality.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(Request $request)
    {
        $query = $request->get('q');
        $role = $request->get('role');

        $users = User::search($query, ['role' => $role])
            ->orderBy('name')
            ->paginate(15);

        return view('users.index', compact('users', 'query', 'role'));
    }

    /**
     * Search users with advanced filters
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function search(Request $request)
    {
        $query = $request->input('keyword');
        $role = $request->input('role');

        $users = User::search($query, ['role' => $role])
            ->orderBy('name')
            ->paginate(15);

        return view('users.index', [
            'users' => $users,
            'keyword' => $query,
            'role' => $role
        ]);
    }

    /**
     * Show user details.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }
}
