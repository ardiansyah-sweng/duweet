<?php

namespace Database\Factories;

use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker; // <-- 1. Tambahkan import ini

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinancialAccount>
 */
class FinancialAccountFactory extends Factory
{
    public function definition(): array
    {
        // 2. Tambahkan baris ini untuk membuat data dalam Bahasa Indonesia
        $faker = Faker::create('id_ID');

        return [
            'parent_id' => null,
            // 3. Ini akan membuat nama perusahaan/bank palsu dalam B. Indonesia
            'name' => $faker->unique()->company(),
            'type' => $faker->randomElement(AccountType::cases()),
            'balance' => $faker->numberBetween(100000, 10000000),
            'initial_balance' => 0,
            'is_group' => false,
            // 4. Ini akan membuat deskripsi dalam B. Indonesia
            'description' => $faker->sentence(),
            'is_active' => true,
            'color' => $faker->hexColor(),
            'icon' => 'default-icon',
            'sort_order' => 0,
            'level' => 0,
        ];
    }

    /**
     * State untuk menandakan ini adalah Akun Grup (tidak bisa punya transaksi)
     */
    public function group(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_group' => true,
                'balance' => 0,
                'initial_balance' => 0,
            ];
        });
    }
}