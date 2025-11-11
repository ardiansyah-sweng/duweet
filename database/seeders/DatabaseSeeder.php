<?php

namespace Database\Seeders;

<<<<<<< HEAD
use App\Models\User;
=======
>>>>>>> 42bc9f3bbf5a55c80294b126bd1d842b97ae94cb
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
<<<<<<< HEAD
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name'              => 'Test User',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'remember_token'    => Str::random(10),
                'usia'              => 21,
                'bulan_lahir'       => 8,
                'tahun_lahir'       => 2002,
                'tanggal_lahir'     => '2002-08-15',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        );

        $this->call([
            DemoDataSeeder::class,
=======
        $this->call([
            UserSeeder::class,
            UserAccountSeeder::class,
            //AccountSeeder::class,
>>>>>>> 42bc9f3bbf5a55c80294b126bd1d842b97ae94cb
        ]);
    }
}
