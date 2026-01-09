<?php

namespace Database\Factories;

use App\Constants\TransactionColumns;
use App\Models\FinancialAccount;
use App\Models\UserAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        // Pick a random INCOME account
        $incomeAccountId = FinancialAccount::where('type', 'IN')->inRandomOrder()->value('id');
        // Fallback: if none exists, create a temporary income account
        if (!$incomeAccountId) {
            $incomeAccountId = FinancialAccount::factory()->create([
                'name' => 'Temp Income',
                'type' => 'IN',
                'is_group' => false,
                'level' => 1,
            ])->id;
        }

        $userAccountId = UserAccount::inRandomOrder()->value('id');

        // Random date in last 12 months
        $createdAt = $this->faker->dateTimeBetween('-12 months', 'now');

        return [
            TransactionColumns::TRANSACTION_GROUP_ID => (string) Str::uuid(),
            TransactionColumns::USER_ACCOUNT_ID => $userAccountId,
            TransactionColumns::FINANCIAL_ACCOUNT_ID => $incomeAccountId,
            TransactionColumns::ENTRY_TYPE => 'credit',
            TransactionColumns::AMOUNT => $this->faker->numberBetween(50000, 5000000),
            TransactionColumns::BALANCE_EFFECT => 'increase',
            TransactionColumns::DESCRIPTION => $this->faker->sentence(6),
            TransactionColumns::IS_BALANCE => false,
            TransactionColumns::CREATED_AT => $createdAt,
            TransactionColumns::UPDATED_AT => $createdAt,
        ];
    }
}
