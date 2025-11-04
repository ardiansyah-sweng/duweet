<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Constants\UserAccountColumns;

class UserAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks while truncating
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        DB::table('user_accounts')->truncate();

        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Ensure at least one user exists to satisfy foreign key
        $user = User::first();
        if (! $user) {
            $user = User::factory()->create([
                'name' => 'Seed User',
                'email' => 'seeduser@example.com',
            ]);
        }

        $now = now();

        $accounts = [
            [
                UserAccountColumns::ID_USER => $user->id,
                UserAccountColumns::USERNAME => 'testuser',
                UserAccountColumns::EMAIL => 'test@example.com',
                UserAccountColumns::PASSWORD => bcrypt('secret123'),
                UserAccountColumns::VERIFIED_AT => $now,
                UserAccountColumns::IS_ACTIVE => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                UserAccountColumns::ID_USER => $user->id,
                UserAccountColumns::USERNAME => 'resetme',
                UserAccountColumns::EMAIL => 'resetme@example.com',
                UserAccountColumns::PASSWORD => bcrypt('oldpassword'),
                UserAccountColumns::VERIFIED_AT => null,
                UserAccountColumns::IS_ACTIVE => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                UserAccountColumns::ID_USER => $user->id,
                UserAccountColumns::USERNAME => 'bob',
                UserAccountColumns::EMAIL => 'bob@example.com',
                UserAccountColumns::PASSWORD => bcrypt('bobpass'),
                UserAccountColumns::VERIFIED_AT => $now,
                UserAccountColumns::IS_ACTIVE => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('user_accounts')->insert($accounts);
    }
}
