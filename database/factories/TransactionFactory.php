<?php

namespace Database\Factories;

use App\Constants\TransactionColumns;
use App\Models\UserAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            TransactionColumns::TRANSACTION_GROUP_ID => (string) Str::uuid(),
            TransactionColumns::USER_ACCOUNT_ID => null, // Will be set in seeder
            TransactionColumns::FINANCIAL_ACCOUNT_ID => null, // Will be set in seeder
            TransactionColumns::ENTRY_TYPE => null, // Will be set via debit()/credit()
            TransactionColumns::AMOUNT => null, // Will be set in seeder/test
            TransactionColumns::BALANCE_EFFECT => null, // Will be set via increase()/decrease()
            TransactionColumns::DESCRIPTION => $this->faker->sentence(6), // Can be overridden
            TransactionColumns::IS_BALANCE => null, // Will be set via balanced()
            TransactionColumns::CREATED_AT => null, // Will be set via onDate()
            TransactionColumns::UPDATED_AT => now(),
        ];
    }

    /**
     * Set transaction group ID for paired debit-credit transactions
     */
    public function withGroupId(string $groupId): self
    {
        return $this->state(fn (array $attributes) => [
            TransactionColumns::TRANSACTION_GROUP_ID => $groupId,
        ]);
    }

    /**
     * Create a debit transaction
     */
    public function debit(): self
    {
        return $this->state(fn (array $attributes) => [
            TransactionColumns::ENTRY_TYPE => 'debit',
        ]);
    }

    /**
     * Create a credit transaction
     */
    public function credit(): self
    {
        return $this->state(fn (array $attributes) => [
            TransactionColumns::ENTRY_TYPE => 'credit',
        ]);
    }

    /**
     * Set balance effect to increase
     */
    public function increase(): self
    {
        return $this->state(fn (array $attributes) => [
            TransactionColumns::BALANCE_EFFECT => 'increase',
        ]);
    }

    /**
     * Set balance effect to decrease
     */
    public function decrease(): self
    {
        return $this->state(fn (array $attributes) => [
            TransactionColumns::BALANCE_EFFECT => 'decrease',
        ]);
    }

    /**
     * Mark as balanced transaction
     */
    public function balanced(): self
    {
        return $this->state(fn (array $attributes) => [
            TransactionColumns::IS_BALANCE => true,
        ]);
    }

    /**
     * Set specific date for the transaction
     */
    public function onDate($date): self
    {
        return $this->state(fn (array $attributes) => [
            TransactionColumns::CREATED_AT => $date,
        ]);
    }
}
