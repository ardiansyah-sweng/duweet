<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserAccount;
use Illuminate\Support\Facades\DB;

class UserAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table untuk fresh start - support SQLite dan MySQL
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        
        UserAccount::truncate();
        
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        User::all()->each(function (User $user) {
            $count = rand(1, 3);

            $willCopyEmail = (bool) rand(0, 1);
            $copyIndex = $willCopyEmail ? rand(0, $count - 1) : null;

            for ($i = 0; $i < $count; $i++) {
                try {
                    $factory = UserAccount::factory()->for($user, 'user');

                    if ($copyIndex !== null && $i === $copyIndex) {
                        $factory = $factory->useUserEmail($user->email);
                    }

                    $factory->create();
                } catch (\Exception $e) {
                    // Skip jika ada error (misalnya duplicate)
                }
            }
        });
    }
}
