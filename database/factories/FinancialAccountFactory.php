<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FinancialAccount;
use App\Constants\FinancialAccountColumns;

class FinancialAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FinancialAccount::class;

    /**
     * Define the model's default state.
     */
    public function definition()
    {
        return [
            FinancialAccountColumns::PARENT_ID => null,
            FinancialAccountColumns::NAME => $this->faker->unique()->words(2, true),
            FinancialAccountColumns::TYPE => $this->faker->randomElement(['AS','LI','IN','EX','SP']),
            FinancialAccountColumns::BALANCE => $this->faker->numberBetween(0, 2000000),
            FinancialAccountColumns::INITIAL_BALANCE => $this->faker->numberBetween(0, 1000000),
            FinancialAccountColumns::IS_GROUP => $this->faker->boolean(30),
            FinancialAccountColumns::IS_ACTIVE => true,
            FinancialAccountColumns::SORT_ORDER => $this->faker->numberBetween(0, 10),
            FinancialAccountColumns::LEVEL => 0,
            FinancialAccountColumns::DESCRIPTION => $this->faker->sentence(),
        ];
    }

    /** Optional state for group accounts */
    public function group()
    {
        return $this->state(fn(array $attributes) => [
            FinancialAccountColumns::IS_GROUP => true,
        ]);
    }
}
