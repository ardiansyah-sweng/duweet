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
            AccountColumns::NAME => $this->faker->randomElement([
                'Cash','BCA','BRI','Dana','OVO','Gopay','Tabungan','Reksa Dana'
            ]),

            AccountColumns::PARENT_ID => null,

            // default type neutral (akan di-override dengan state)
            AccountColumns::TYPE => 'AS',

            AccountColumns::BALANCE => $this->faker->numberBetween(0, 5_000_000),
            AccountColumns::INITIAL_BALANCE => 0,

            // akun group sebaiknya dibuat dengan state khusus
            AccountColumns::IS_GROUP => false,

            AccountColumns::DESCRIPTION => $this->faker->optional()->sentence(),
            AccountColumns::IS_ACTIVE => true,
            AccountColumns::SORT_ORDER => $this->faker->numberBetween(0, 10),
            AccountColumns::LEVEL => 0,
        ];
    }

    // STATE: akun pendapatan
    public function income(): self
    {
        return $this->state(fn () => [
            AccountColumns::TYPE => 'IN',
            AccountColumns::IS_GROUP => false,
        ]);
    }

    // STATE: akun pengeluaran
    public function expense(): self
    {
        return $this->state(fn () => [
            AccountColumns::TYPE => 'EX',
            AccountColumns::IS_GROUP => false,
        ]);
    }

    // STATE: akun grup (opsional)
    public function group(): self
    {
        return $this->state(fn () => [
            AccountColumns::IS_GROUP => true,
        ]);
    }
}
