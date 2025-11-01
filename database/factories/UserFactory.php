<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $birthYear = fake()->numberBetween(1960, 2005);
        $birthMonth = fake()->numberBetween(1, 12);
        $birthDay = fake()->numberBetween(1, 28);
        $currentYear = date('Y');
        $age = $currentYear - $birthYear;

        return [
            'name' => fake()->name(),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'tanggal_lahir' => $birthDay,
            'bulan_lahir' => $birthMonth,
            'tahun_lahir' => $birthYear,
            'usia' => $age,
        ];
    }
}
