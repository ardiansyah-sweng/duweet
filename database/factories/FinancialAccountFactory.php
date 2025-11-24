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
        $types = ['IN','EX','SP','LI','AS'];

        return [
            AccountColumns::NAME => $this->faker->randomElement(['Cash','BCA','BRI','Dana','OVO','Gopay','Tabungan','Reksa Dana']),
            AccountColumns::PARENT_ID => null,
            AccountColumns::TYPE => $this->faker->randomElement($types),
            AccountColumns::BALANCE => $this->faker->numberBetween(0, 5_000_000),
            AccountColumns::INITIAL_BALANCE => 0,
            AccountColumns::IS_GROUP => $this->faker->boolean(10),
            AccountColumns::DESCRIPTION => $this->faker->optional()->sentence(),
            AccountColumns::IS_ACTIVE => true,
            AccountColumns::SORT_ORDER => $this->faker->numberBetween(0, 10),
            AccountColumns::LEVEL => 0,
        ];
    }
}
