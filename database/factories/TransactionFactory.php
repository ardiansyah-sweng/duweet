<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Constants\TransactionColumns;
use App\Models\UserAccount;
use App\Models\FinancialAccount;
use Illuminate\Support\Str;

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            TransactionColumns::TRANSACTION_GROUP_ID => Str::uuid(),

            // Relasi
            TransactionColumns::USER_ACCOUNT_ID => UserAccount::factory(),
            TransactionColumns::FINANCIAL_ACCOUNT_ID => FinancialAccount::factory(),

            // Entry type: debit/kredit
            TransactionColumns::ENTRY_TYPE => $this->faker->randomElement(['debit', 'kredit']),

            // Nominal transaksi
            TransactionColumns::AMOUNT => $this->faker->numberBetween(10000, 500000),

            // increase / decrease
            TransactionColumns::BALANCE_EFFECT => $this->faker->randomElement(['increase', 'decrease']),

            // Deskripsi
            TransactionColumns::DESCRIPTION => $this->faker->sentence(),

            // Apakah mempengaruhi saldo
            TransactionColumns::IS_BALANCE => true,

            // Tanggal transaksi acak dalam 6 bulan terakhir
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * State khusus untuk transaksi income.
     */
    public function income(): self
    {
        return $this->state(fn () => [
            TransactionColumns::BALANCE_EFFECT => 'increase',
        ]);
    }

    /**
     * State khusus untuk transaksi expense.
     */
    public function expense(): self
    {
        return $this->state(fn () => [
            TransactionColumns::BALANCE_EFFECT => 'decrease',
        ]);
    }

    /**
     * Khusus untuk transaksi yang benar-benar mempengaruhi saldo.
     */
    public function isBalance(bool $flag = true): self
    {
        return $this->state(fn () => [
            TransactionColumns::IS_BALANCE => $flag,
        ]);
    }
}
