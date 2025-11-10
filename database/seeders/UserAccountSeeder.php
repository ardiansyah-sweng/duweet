<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserAccount;
use App\Constants\UserAccountColumns;

class UserAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserAccount::create([
            UserAccountColumns::USERNAME => 'ardian2007',
            UserAccountColumns::EMAIL => 'ardiansyah.2019@outlook.com',
            UserAccountColumns::PASSWORD => bcrypt('password'),
            UserAccountColumns::VERIFIED_AT => now(),
            UserAccountColumns::IS_ACTIVE => true,
        ]);
    }
}
