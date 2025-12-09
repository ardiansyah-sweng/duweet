<?php

namespace Database\Factories;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FinancialAccount; // Pastikan model di-import

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinancialAccount>
 */
class FinancialAccountFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = FinancialAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $accountType = $this->faker->randomElement(['income', 'expense', 'asset', 'liability']);
        
        $name = 'Akun ' . $accountType . ' ' . $this->faker->word();

        return [
            'name' => $name,
            'description' => $this->faker->sentence(),
            'account_type' => $accountType,
            'is_active' => true,
        ];
    }

 
    public function income(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Akun Pemasukan',
            'account_type' => 'income',
        ]);
    }

    public function expense(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Akun Pengeluaran',
            'account_type' => 'expense',
        ]);
    }
}
=======
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
>>>>>>> 1ddf2b86ee702e9d70eeccf8ccd250a7abec4494
