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
