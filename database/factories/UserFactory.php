<?php

namespace Database\Factories;

use App\Models\User;
use App\Constants\UserColumns;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $birthYear = $this->faker->numberBetween(1980, 2005);
        $birthMonth = $this->faker->numberBetween(1, 12);
        $birthDay = $this->faker->numberBetween(1, 28);
        $age = date('Y') - $birthYear;

        return [
            UserColumns::NAME           => $this->faker->name(),
            UserColumns::FIRST_NAME     => $this->faker->firstName(),
            UserColumns::MIDDLE_NAME    => $this->faker->optional()->firstName(),
            UserColumns::LAST_NAME      => $this->faker->lastName(),
            UserColumns::EMAIL          => $this->faker->unique()->safeEmail(),

            UserColumns::PROVINSI       => $this->faker->state(),
            UserColumns::KABUPATEN      => $this->faker->city(),
            UserColumns::KECAMATAN      => $this->faker->citySuffix(),
            UserColumns::JALAN          => $this->faker->streetAddress(),
            UserColumns::KODE_POS       => $this->faker->postcode(),

            UserColumns::TANGGAL_LAHIR  => $birthDay,
            UserColumns::BULAN_LAHIR    => $birthMonth,
            UserColumns::TAHUN_LAHIR    => $birthYear,
            UserColumns::USIA           => $age,
        ];
    }
}
