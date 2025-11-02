<?php

namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserAccount;

class UserAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all(); // ambil semua user yang sudah dibuat

        foreach ($users as $user) {
            UserAccount::factory()->create([
                'id_user' => $user->id,     // relasi ke user
                'email'   => $user->email,  // sinkron email
            ]);
        }
    }
}
