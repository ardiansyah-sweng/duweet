<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { 
    
    DB::table('user_accounts')->insert([
    'user_id' => 1,
    'username' => 'abyan',
    'email' => 'abyan06@gmail.com',
    'password' => Hash::make('informatika123'),
    'email_verified_at' => now(),
    'is_active' => true,

    ]);


    }
}
