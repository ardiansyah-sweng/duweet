<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\UserAccount;
use App\Constants\TransactionColumns;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            TransactionColumns::TRANSACTION_GROUP_ID => Str::uuid()->toString(),
            TransactionColumns::USER_ACCOUNT_ID => UserAccount::factory(),
            TransactionColumns::FINANCIAL_ACCOUNT_ID => null, // Will be set by seeder
            TransactionColumns::ENTRY_TYPE => fake()->randomElement(['debit', 'credit']),
            TransactionColumns::AMOUNT => fake()->numberBetween(10000, 1000000),
            TransactionColumns::BALANCE_EFFECT => fake()->randomElement(['increase', 'decrease']),
            TransactionColumns::DESCRIPTION => fake()->sentence(),
            TransactionColumns::IS_BALANCE => false,
            TransactionColumns::CREATED_AT => fake()->dateTimeBetween('-30 days', 'now'),
            TransactionColumns::UPDATED_AT => now(),
        ];
    }

    /**
     * Create a balanced transaction pair (debit + credit with same amount and group_id)
     * This follows double-entry bookkeeping principles where every transaction
     * must have equal debit and credit entries.
     *
     * @param int $userAccountId User account ID
     * @param int $debitAccountId Financial account for debit entry
     * @param int $creditAccountId Financial account for credit entry
     * @param int $amount Transaction amount
     * @param string $description Transaction description
     * @return array Array containing [debit_transaction, credit_transaction]
     */
    public function balancedPair(
        int $userAccountId,
        int $debitAccountId,
        int $creditAccountId,
        int $amount,
        string $description
    ): array {
        $groupId = Str::uuid()->toString();
        $timestamp = fake()->dateTimeBetween('-30 days', 'now');

        // Debit entry
        $debit = [
            TransactionColumns::TRANSACTION_GROUP_ID => $groupId,
            TransactionColumns::USER_ACCOUNT_ID => $userAccountId,
            TransactionColumns::FINANCIAL_ACCOUNT_ID => $debitAccountId,
            TransactionColumns::ENTRY_TYPE => 'debit',
            TransactionColumns::AMOUNT => $amount,
            TransactionColumns::BALANCE_EFFECT => 'increase',
            TransactionColumns::DESCRIPTION => $description,
            TransactionColumns::IS_BALANCE => true, // Marked as balanced
            TransactionColumns::CREATED_AT => $timestamp,
            TransactionColumns::UPDATED_AT => $timestamp,
        ];

        // Credit entry
        $credit = [
            TransactionColumns::TRANSACTION_GROUP_ID => $groupId,
            TransactionColumns::USER_ACCOUNT_ID => $userAccountId,
            TransactionColumns::FINANCIAL_ACCOUNT_ID => $creditAccountId,
            TransactionColumns::ENTRY_TYPE => 'credit',
            TransactionColumns::AMOUNT => $amount,
            TransactionColumns::BALANCE_EFFECT => 'decrease',
            TransactionColumns::DESCRIPTION => $description,
            TransactionColumns::IS_BALANCE => true, // Marked as balanced
            TransactionColumns::CREATED_AT => $timestamp,
            TransactionColumns::UPDATED_AT => $timestamp,
        ];

        return [$debit, $credit];
    }
}
