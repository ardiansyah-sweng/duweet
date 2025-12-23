<?php

namespace App\Http\Controllers;

use App\Constants\UserColumns;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    /**
     * Return list of users with accounts_count.
     * If denormalized column exists, use it; otherwise use withCount.
     */
    public function index()
    {
        if (Schema::hasColumn('users', UserColumns::ACCOUNTS_COUNT)) {
            $users = User::select('id', 'name', UserColumns::ACCOUNTS_COUNT)->get();
        } else {
            $users = User::withCount('userAccounts')->get()->map(function ($u) {
                $u->accounts_count = $u->user_accounts_count;
                unset($u->user_accounts_count);
                return $u;
            });
        }

        return response()->json(['success' => true, 'data' => $users]);
    }

    /**
     * Return single user with accounts_count
     */
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        if (Schema::hasColumn('users', UserColumns::ACCOUNTS_COUNT)) {
            $user->accounts_count = $user->{UserColumns::ACCOUNTS_COUNT};
        } else {
            $user->loadCount('userAccounts');
            $user->accounts_count = $user->user_accounts_count;
        }

        return response()->json(['success' => true, 'data' => $user]);
    }

    /**
     * Trigger recount via artisan command and return status.
     */
    public function recount()
    {
        // call the artisan command programmatically
        Artisan::call('accounts:recount');
        $output = Artisan::output();
        return response()->json(['success' => true, 'message' => 'Recount started', 'output' => $output]);
    }
}
