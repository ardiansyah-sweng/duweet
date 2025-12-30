<?php

namespace Database\Factories;

use App\Constants\TransactionColumns;
use App\Models\Transaction;
use App\Models\UserAccount;
use App\Models\FinancialAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Transaction Factory
 *
 * Creates balanced transaction pairs following double-entry bookkeeping principles.
 * Each transaction has a debit and credit entry with the same transaction_group_id.
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

    /**
     * Create a balanced transaction pair (debit + credit) following double-entry bookkeeping.
     * 
     * Balance effect is determined by account type and entry type:
     * - Asset/Expense (AS/EX): Debit increases, Credit decreases
     * - Income/Liability (IN/LI): Debit decreases, Credit increases
     * - Spending (SP): Debit increases, Credit decreases
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

        // Get account types to determine balance effect
        $debitAccount = DB::table(config('db_tables.financial_account'))->find($debitAccountId);
        $creditAccount = DB::table(config('db_tables.financial_account'))->find($creditAccountId);

        // Determine balance effect based on account type and entry type
        // Asset (AS), Expense (EX), Spending (SP): Debit increases, Credit decreases
        // Income (IN), Liability (LI): Debit decreases, Credit increases
        $debitIncreaseTypes = ['AS', 'EX', 'SP'];
        $creditIncreaseTypes = ['IN', 'LI'];

        $debitBalanceEffect = in_array($debitAccount->type, $debitIncreaseTypes) ? 'increase' : 'decrease';
        $creditBalanceEffect = in_array($creditAccount->type, $creditIncreaseTypes) ? 'increase' : 'decrease';

        // Debit entry
        $debit = [
            TransactionColumns::TRANSACTION_GROUP_ID => $groupId,
            TransactionColumns::USER_ACCOUNT_ID => $userAccountId,
            TransactionColumns::FINANCIAL_ACCOUNT_ID => $debitAccountId,
            TransactionColumns::ENTRY_TYPE => 'debit',
            TransactionColumns::AMOUNT => $amount,
            TransactionColumns::BALANCE_EFFECT => $debitBalanceEffect,
            TransactionColumns::DESCRIPTION => $description,
            TransactionColumns::IS_BALANCE => true,
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
            TransactionColumns::BALANCE_EFFECT => $creditBalanceEffect,
            TransactionColumns::DESCRIPTION => $description,
            TransactionColumns::IS_BALANCE => true,
            TransactionColumns::CREATED_AT => $timestamp,
            TransactionColumns::UPDATED_AT => $timestamp,
        ];

        return [$debit, $credit];
    }
}
