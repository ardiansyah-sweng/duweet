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
            UserAccount::truncate();
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            UserAccount::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        User::all()->each(function (User $user) {
            $count = rand(1, 3);

            for ($i = 0; $i < $count; $i++) {
                try {
                    $factory = UserAccount::factory()->for($user, 'user');
                    
                    // 50% chance untuk menggunakan email dari user
                    if ($i === 0 && rand(0, 1)) {
                        $factory = $factory->useUserEmail($user->email);
                    }

                    $factory->create();
                } catch (\Exception $e) {
                    // Skip jika ada error (misalnya duplicate)
                    // Log error jika diperlukan
                    \Log::warning("Failed to create UserAccount for user {$user->id}: " . $e->getMessage());
                }
            }
        });
    }
}
