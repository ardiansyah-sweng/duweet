<?php

namespace Database\Seeders;

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
        User::all()->each(function (User $user) {
            $count = rand(1, 3);

            $willCopyEmail = (bool) rand(0, 1);
            $copyIndex = $willCopyEmail ? rand(0, $count - 1) : null;

            for ($i = 0; $i < $count; $i++) {
                $factory = UserAccount::factory()->for($user, 'user');

                if ($copyIndex !== null && $i === $copyIndex) {
                    $factory = $factory->useUserEmail($user->email);
                }

                $factory->create();
            }
        });
    }
}
