<?php

namespace Database\Factories;

use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinancialAccount>
 */
class FinancialAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ambil tipe akun acak dari Enum kamu
        $type = $this->faker->randomElement(AccountType::cases());

        return [
            'parent_id' => null,
            'name' => $this->faker->words(2, true), // cth: "Dompet Tunai"
            'type' => $type,
            'balance' => $this->faker->numberBetween(50000, 2000000),
            'initial_balance' => $this->faker->numberBetween(50000, 2000000),
            'is_group' => false, // Default: BUKAN grup
            'is_active' => true,
            'level' => 0,
        ];
    }

    /**
     * Menandakan bahwa akun ini adalah grup.
     */
    public function group(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_group' => true,
            'balance' => 0, // Grup tidak punya saldo sendiri
            'initial_balance' => 0,
        ]);
    }
}