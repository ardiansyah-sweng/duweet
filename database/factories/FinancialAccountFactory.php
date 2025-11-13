<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FinancialAccount; // Pastikan model di-import

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinancialAccount>
 */
class FinancialAccountFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = FinancialAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $accountType = $this->faker->randomElement(['income', 'expense', 'asset', 'liability']);
        
        $name = 'Akun ' . $accountType . ' ' . $this->faker->word();

        return [
            'name' => $name,
            'description' => $this->faker->sentence(),
            'account_type' => $accountType,
            'is_active' => true,
        ];
    }

 
    public function income(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Akun Pemasukan',
            'account_type' => 'income',
        ]);
    }

    public function expense(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Akun Pengeluaran',
            'account_type' => 'expense',
        ]);
    }
}