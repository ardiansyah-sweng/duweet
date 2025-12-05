<?php

namespace Database\Factories;

use App\Constants\FinancialAccountColumns;
use App\Models\FinancialAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinancialAccount>
 */
class FinancialAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = FinancialAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            FinancialAccountColumns::PARENT_ID => null,
            FinancialAccountColumns::NAME => $this->faker->unique()->word() . ' Account',
            FinancialAccountColumns::TYPE => $this->faker->randomElement(['IN', 'EX', 'SP', 'LI', 'AS']),
            FinancialAccountColumns::BALANCE => $this->faker->numberBetween(0, 10000000),
            FinancialAccountColumns::INITIAL_BALANCE => $this->faker->numberBetween(0, 5000000),
            FinancialAccountColumns::IS_GROUP => false,
            FinancialAccountColumns::DESCRIPTION => $this->faker->sentence(3),
            FinancialAccountColumns::IS_ACTIVE => true,
            FinancialAccountColumns::SORT_ORDER => $this->faker->numberBetween(0, 10),
            FinancialAccountColumns::LEVEL => 0,
        ];
    }
}
