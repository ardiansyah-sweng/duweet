<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserLoginSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user_login')->insert([
            [
                'user_account_id' => 1,
                'last_login_at' => Carbon::create(2026, 1, rand(1, 31)),
            ],
            [
                'user_account_id' => 2,
                'last_login_at' => Carbon::create(2026, 1, rand(1, 31)),
            ],
            [
                'user_account_id' => 3,
                'last_login_at' => null, // simulasi belum pernah login
            ],
            [
                'user_account_id' => 4,
                'last_login_at' => Carbon::create(2026, 1, rand(1, 31)),
            ],
        ]);
    }
}