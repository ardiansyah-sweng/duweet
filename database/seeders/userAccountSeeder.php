<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserAccount;
use App\Models\User;
use Illuminate\Support\Facades\DB;    
use Illuminate\Support\Facades\Hash;

class UserAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan FK sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        UserAccount::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $userIds = DB::table('users')->pluck('id');

        $accounts = [];
        foreach ($userIds as $i => $id) {
            $accounts[] = [
                'user_id' => $id,
                'username' => 'user_acc_' . ($i+1),
                'email' => 'user_acc' . ($i+1) . '@example.com',
                'password' => Hash::make('acc12345'),
                'email_verified_at' => now(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

DB::table('user_accounts')->insert($accounts);

    }
}
