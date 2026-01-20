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
        // Nonaktifkan FK sementara (support MySQL dan SQLite)
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        }
        
        UserTelephone::truncate();
        
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        // Ambil semua id user
        $userIds = DB::table(config('db_tables.users', 'users'))->pluck('id');

        $telephones = [];
        foreach ($userIds as $i => $id) {
            $telephones[] = [
                Columns::USER_ID => $id,
                Columns::NUMBER => '0812345678' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table(config('db_tables.user_telephone', 'user_telephones'))->insert($telephones);
    }
}
