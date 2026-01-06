<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class UserAccountCountController extends Controller
{
   
    public function index(Request $request)
    {
        $onlyActive = $request->query('only_active', '1') === '1';
        $cacheKey = 'users.accounts.count:onlyActive=' . ($onlyActive ? '1' : '0');

        if (app()->environment('testing')) {
            $results = User::countFinancialAccountsPerUser($onlyActive);
        } else {
            $results = Cache::remember($cacheKey, 60, function () use ($onlyActive) {
                return User::countFinancialAccountsPerUser($onlyActive);
            });
        }

        return response()->json($results);
    }
}
