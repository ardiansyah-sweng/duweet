<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class UserAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a user_account for every user if it doesn't already exist.
        $uaTable = config('db_tables.user_account', 'user_accounts');

        foreach (User::all() as $user) {
            $exists = DB::table($uaTable)->where('id_user', $user->id)->first();
            if ($exists) {
                continue;
            }

            // create a simple unique username based on id
            $username = 'user' . $user->id;
            $row = [
                'id_user' => $user->id,
                'username' => $username,
                'email' => $user->email,
                'password' => bcrypt('password'),
                'verified_at' => now(),
                'is_active' => true,
            ];

            // Only add timestamps if the table actually has them (some migrations may omit timestamps)
            if (Schema::hasColumn($uaTable, 'created_at')) {
                $row['created_at'] = now();
            }
            if (Schema::hasColumn($uaTable, 'updated_at')) {
                $row['updated_at'] = now();
            }

            DB::table($uaTable)->insert($row);
        }
    }
}
