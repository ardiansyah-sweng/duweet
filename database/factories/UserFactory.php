<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->boolean(50) ? fake()->firstName() : null,
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            
            // Address data
            'provinsi' => fake()->state(),
            'kabupaten' => fake()->city(),
            'kecamatan' => fake()->cityPrefix() . ' ' . fake()->citySuffix(),
            'jalan' => fake()->streetAddress(),
            'kode_pos' => fake()->postcode(),
            
            // Birth data
            'tanggal_lahir' => fake()->numberBetween(1, 31),
            'bulan_lahir' => fake()->numberBetween(1, 12),
            'tahun_lahir' => fake()->numberBetween(1950, 2010),
            'usia' => function (array $attributes) {
                $currentYear = now()->year;
                return $currentYear - $attributes['tahun_lahir'];
            },
        ];
    }
}
