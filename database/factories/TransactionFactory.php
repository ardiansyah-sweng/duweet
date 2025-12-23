<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Transaction;
use App\Models\UserAccount;
use App\Models\FinancialAccount;
use App\Constants\TransactionColumns;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $userAccountId = UserAccount::inRandomOrder()->first()?->id ?? UserAccount::factory()->create()->id;
        $financialAccountId = FinancialAccount::inRandomOrder()->first()?->id ?? 1;

        $entryType = $this->faker->randomElement(['debit', 'credit']);
        $balanceEffect = ($entryType === 'debit') ? 'decrease' : 'increase';

        return [
            TransactionColumns::TRANSACTION_GROUP_ID => Str::uuid()->toString(),
            TransactionColumns::USER_ACCOUNT_ID => $userAccountId,
            TransactionColumns::FINANCIAL_ACCOUNT_ID => $financialAccountId,
            TransactionColumns::ENTRY_TYPE => $entryType,
            TransactionColumns::AMOUNT => $this->faker->numberBetween(10000, 1000000),
            TransactionColumns::BALANCE_EFFECT => $balanceEffect,
            TransactionColumns::DESCRIPTION => $this->faker->sentence(4),
            TransactionColumns::IS_BALANCE => $this->faker->boolean(10),
            TransactionColumns::CREATED_AT => $this->faker->dateTimeBetween('-1 year', 'now'),
            TransactionColumns::UPDATED_AT => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function balancedPair(
        int $userAccountId,
        int $debitAccountId,
        int $creditAccountId,
        int $amount,
        string $description
    ): array {
        $groupId = Str::uuid()->toString();
        $timestamp = $this->faker->dateTimeBetween('-30 days', 'now');

        $debitAccount = DB::table(config('db_tables.financial_account'))->find($debitAccountId);
        $creditAccount = DB::table(config('db_tables.financial_account'))->find($creditAccountId);

        $debitIncreaseTypes = ['AS', 'EX', 'SP'];
        $creditIncreaseTypes = ['IN', 'LI'];

        $debitBalanceEffect = in_array($debitAccount->type, $debitIncreaseTypes) ? 'increase' : 'decrease';
        $creditBalanceEffect = in_array($creditAccount->type, $creditIncreaseTypes) ? 'increase' : 'decrease';

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