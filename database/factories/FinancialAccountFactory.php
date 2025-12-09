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
        $accountNames = ['Cash', 'BCA', 'BRI', 'BNI', 'Mandiri', 'Dana', 'OVO', 'Gopay', 'ShopeePay', 'Tabungan', 'Reksa Dana', 'Deposito'];
        $types = ['IN', 'EX', 'SP', 'LI', 'AS'];

        return [
            FinancialAccountColumns::NAME => $this->faker->randomElement($accountNames),
            FinancialAccountColumns::PARENT_ID => null,
            FinancialAccountColumns::TYPE => $this->faker->randomElement($types),
            FinancialAccountColumns::BALANCE => $this->faker->numberBetween(0, 5_000_000),
            FinancialAccountColumns::INITIAL_BALANCE => $this->faker->numberBetween(0, 1_000_000),
            FinancialAccountColumns::IS_GROUP => $this->faker->boolean(10),
            FinancialAccountColumns::DESCRIPTION => $this->faker->optional()->sentence(3),
            FinancialAccountColumns::IS_ACTIVE => true,
            FinancialAccountColumns::SORT_ORDER => $this->faker->numberBetween(0, 10),
            FinancialAccountColumns::LEVEL => 0,
        ];
    }
}
