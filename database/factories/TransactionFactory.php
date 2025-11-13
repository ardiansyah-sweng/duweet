<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Transaction;
use App\Models\UserAccount;
use App\Models\FinancialAccount;
use App\Constants\TransactionColumns;
use Illuminate\Support\Str;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {

        $userAccountId = UserAccount::inRandomOrder()->first()?->id ?? UserAccount::factory()->create()->id;
        $financialAccountId = FinancialAccount::inRandomOrder()->first()?->id ?? 1;

        $entryType = $this->faker->randomElement(['debit', 'kredit']);
        
        // Asumsi Debit = Expense/Decrease, Kredit = Income/Increase
        $balanceEffect = ($entryType === 'debit') ? 'decrease' : 'increase';

        return [
            TransactionColumns::USER_ACCOUNT_ID => $userAccountId,
            TransactionColumns::FINANCIAL_ACCOUNT_ID => $financialAccountId,
            TransactionColumns::TRANSACTION_GROUP_ID => Str::uuid()->toString(),
            
            TransactionColumns::ENTRY_TYPE => $entryType,
            TransactionColumns::AMOUNT => $this->faker->numberBetween(10000, 1000000), 
            TransactionColumns::BALANCE_EFFECT => $balanceEffect,
            TransactionColumns::DESCRIPTION => $this->faker->sentence(4),
            TransactionColumns::IS_BALANCE => $this->faker->boolean(10),
            
            TransactionColumns::CREATED_AT => $this->faker->dateTimeBetween('-1 year', 'now'),
            TransactionColumns::UPDATED_AT => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}