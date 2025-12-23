<?php

namespace Database\Factories;

use App\Models\FinancialAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Constants\FinancialAccountColumns as AccountColumns;

class FinancialAccountFactory extends Factory
{
    protected $model = FinancialAccount::class;

    public function definition(): array
    {
        $types = ['AS', 'IN', 'EX', 'SP']; // Asset, Income, Expense, Spending

        return [
            AccountColumns::NAME => $this->faker->randomElement([
                'Cash', 'BCA', 'BRI', 'Dana', 'OVO', 'Gopay', 'Tabungan', 'Reksa Dana'
            ]),
            AccountColumns::PARENT_ID => null,
            AccountColumns::TYPE => $this->faker->randomElement($types),
            AccountColumns::BALANCE => 0,
            AccountColumns::INITIAL_BALANCE => 0,
            AccountColumns::IS_GROUP => false,
            AccountColumns::DESCRIPTION => $this->faker->sentence(),
            AccountColumns::IS_ACTIVE => true,
            AccountColumns::SORT_ORDER => 1,
            AccountColumns::LEVEL => 1,
        ];
    }
}
