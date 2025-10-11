<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserAccount;

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        // 1. BARIS INI MEMBUAT FAKER VERSI INDONESIA
        $faker = \Faker\Factory::create('id_ID');

        return [
            'user_account_id' => UserAccount::factory(),
            'amount' => $faker->numberBetween(10000, 500000),
            // 2. BARIS INI MENGGUNAKAN FAKER VERSI INDONESIA
            'description' => $faker->sentence(3),
            'transaction_date' => $faker->dateTimeThisMonth(),
        ];
    }
}