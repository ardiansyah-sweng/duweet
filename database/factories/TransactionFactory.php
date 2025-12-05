<?php

namespace Database\Factories;

use App\Constants\TransactionColumns;
use App\Models\Transaction;
use App\Models\UserAccount;
use App\Models\FinancialAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ambil random UserAccount yang sudah ada
        $userAccount = UserAccount::inRandomOrder()->first();
        
        // Ambil random FinancialAccount yang sudah ada
        $financialAccount = FinancialAccount::inRandomOrder()->first();

        return [
            TransactionColumns::TRANSACTION_GROUP_ID => Str::uuid()->toString(),
            TransactionColumns::USER_ACCOUNT_ID => $userAccount?->id ?? 1,
            TransactionColumns::FINANCIAL_ACCOUNT_ID => $financialAccount?->id ?? 1,
            TransactionColumns::ENTRY_TYPE => $this->faker->randomElement(['debit', 'credit']),
            TransactionColumns::AMOUNT => $this->faker->numberBetween(10000, 1000000),
            TransactionColumns::BALANCE_EFFECT => $this->faker->randomElement(['increase', 'decrease']),
            TransactionColumns::DESCRIPTION => $this->faker->sentence(5),
            TransactionColumns::IS_BALANCE => $this->faker->boolean(20),
        ];
    }
}
