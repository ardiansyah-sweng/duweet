<?php

namespace Database\Factories;

use App\Enums\AccountType;
use App\Models\FinancialAccount;
use App\Models\UserAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Faker\Factory as Faker; // <-- 1. Tambahkan import ini

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        // 2. Tambahkan baris ini untuk membuat data dalam Bahasa Indonesia
        $faker = Faker::create('id_ID');

        $userAccount = UserAccount::inRandomOrder()->first() ?? UserAccount::factory()->create();

        $financialAccount = FinancialAccount::where('is_group', false)
            ->inRandomOrder()
            ->first() ?? FinancialAccount::factory()->create(['is_group' => false]);

        $entryType = $faker->randomElement(['debit', 'credit']);
        $accountType = $financialAccount->type;
        $balanceEffect = 'increase';

        switch ($accountType) {
            case AccountType::ASSET:
            case AccountType::EXPENSES:
            case AccountType::SPENDING:
                $balanceEffect = ($entryType === 'debit') ? 'increase' : 'decrease';
                break;
            
            case AccountType::LIABILITY:
            case AccountType::INCOME:
                $balanceEffect = ($entryType === 'credit') ? 'increase' : 'decrease';
                break;
        }

        return [
            'transaction_group_id' => Str::uuid(),
            'user_account_id' => $userAccount->id,
            'financial_account_id' => $financialAccount->id,
            'entry_type' => $entryType,
            'amount' => $faker->numberBetween(10000, 500000),
            'balance_effect' => $balanceEffect,
            // 3. Sekarang ini akan menghasilkan kalimat Bahasa Indonesia
            'description' => $faker->sentence(4), 
            'is_balance' => false,
            'transaction_date' => $faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
