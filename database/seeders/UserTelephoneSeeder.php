<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\UserTelephone;
use App\Constants\UserTelephoneColumns as Columns;

class UserTelephoneSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks to allow truncate (compatible with both MySQL and SQLite)
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }
        
        UserTelephone::truncate();
        
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        // Ambil semua id user
        $userIds = DB::table(config('db_tables.users', 'users'))->pluck('id');

        $telephones = [];
        foreach ($userIds as $i => $id) {
            $telephones[] = [
                Columns::ID_USER => $id,
                Columns::NUMBER => '0812345678' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table(config('db_tables.user_telephone', 'user_telephones'))->insert($telephones);
    }
}
