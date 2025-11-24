<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FinancialAccount;
use App\Constants\FinancialAccountColumns;

class FinancialAccountFactory extends Factory
{
    protected $model = FinancialAccount::class;

    public function definition()
    {
        return [
            FinancialAccountColumns::NAME => $this->faker->word(),
            FinancialAccountColumns::PARENT_ID => null,
            FinancialAccountColumns::TYPE => $this->faker->randomElement(['IN','EX','SP','LI','AS']),
            FinancialAccountColumns::BALANCE => 0,
            FinancialAccountColumns::INITIAL_BALANCE => 0,
            FinancialAccountColumns::IS_GROUP => $this->faker->boolean(10),
            FinancialAccountColumns::DESCRIPTION => $this->faker->optional()->sentence(),
            FinancialAccountColumns::IS_ACTIVE => true,
            FinancialAccountColumns::SORT_ORDER => 0,
            FinancialAccountColumns::LEVEL => 0,
        ];
    }
}
<?php

namespace Database\Factories;

use App\Models\FinancialAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialAccountFactory extends Factory
{
    protected $model = FinancialAccount::class;

    public function definition(): array
    {
        $types = ['cash','bank','ewallet','investment'];

        return [
            'user_id'   => User::factory(),
            'name'      => $this->faker->randomElement(['Cash','BCA','BRI','Dana','OVO','Gopay','Tabungan','Reksa Dana']),
            'type'      => $this->faker->randomElement($types),
            'balance'   => $this->faker->randomFloat(2, 0, 5_000_000),
            'currency'  => 'IDR',
            'is_default'=> $this->faker->boolean(20),
        ];
    }
}
