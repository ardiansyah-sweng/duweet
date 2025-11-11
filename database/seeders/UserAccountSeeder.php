<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Constants\UserAccountColumns;

class UserAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table(config('db_tables.user_account', 'user_accounts'))->insert([
            UserAccountColumns::ID_USER => 1, // Mengacu pada user yang dibuat di DatabaseSeeder
            UserAccountColumns::USERNAME => 'ardian2007',
            UserAccountColumns::EMAIL => 'ardiansyah.2019@outlook.com',
            UserAccountColumns::PASSWORD => bcrypt('password'),
            UserAccountColumns::VERIFIED_AT => now(),
            UserAccountColumns::IS_ACTIVE => true,
        ]);
        
        $this->command->info('UserAccount created: ardian2007');
    }
}
