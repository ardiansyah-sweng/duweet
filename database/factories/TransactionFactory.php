<?php

namespace Database\Factories;

use App\Enums\TransactionBalanceEffect;
use App\Enums\TransactionEntryType;
use App\Models\FinancialAccount;
use App\Models\UserAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // =================================================================
        // PERUBAHAN UTAMA DI SINI
        // Kita tambahkan ".value" di akhir untuk mengambil string ('debit' atau 'credit')
        // dan bukan objek Enum-nya.
        // =================================================================
        $entryType = $this->faker->randomElement(TransactionEntryType::cases())->value;
        $balanceEffect = $this->faker->randomElement(TransactionBalanceEffect::cases())->value;

        return [
            'transaction_group_id' => Str::uuid(),
            'user_account_id' => UserAccount::factory(),
            'financial_account_id' => FinancialAccount::factory(),

            // Sekarang ini akan berisi 'debit' atau 'credit' (string)
            'entry_type' => $entryType,

            'amount' => $this->faker->numberBetween(1000, 1000000),

            // Sekarang ini akan berisi 'increase' atau 'decrease' (string)
            'balance_effect' => $balanceEffect,

            'description' => $this->faker->sentence(3),
            'is_balance' => $this->faker->boolean(80),
        ];
    }
}