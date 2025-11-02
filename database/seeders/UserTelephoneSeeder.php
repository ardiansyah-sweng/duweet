<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;    
use App\Models\UserTelephone;
use App\Models\User;

class UserTelephoneSeeder extends Seeder
{
    public function run(): void
    {

        // Nonaktifkan FK sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        UserTelephone::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $userIds = DB::table('users')->pluck('id');

        $telephones = [];
        foreach ($userIds as $i => $id) {
            $telephones[] = [
                'user_id' => $id,
                'number' => '0812345678' . str_pad($i+1, 2, '0', STR_PAD_LEFT),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

      DB::table('user_telephones')->insert($telephones);

    }
}
