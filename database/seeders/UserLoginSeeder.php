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
                'last_login_at' => Carbon::now()->subDays(10),
            ],
            [
                'user_account_id' => 2,
                'last_login_at' => null,
            ],
            [
                'user_account_id' => 3,
                'last_login_at' => Carbon::now()->subDays(5),
            ],
            [
                'user_account_id' => 4,
                'last_login_at' => Carbon::now()->subDays(1),
            ],
        ]);
    }
}