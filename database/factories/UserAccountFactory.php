<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Constants\UserAccountColumns;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAccount>
 */
class UserAccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            UserAccountColumns::ID_USER => null, 
            UserAccountColumns::USERNAME => $this->faker->unique()->userName(),
            UserAccountColumns::EMAIL => $this->faker->unique()->safeEmail(),
            UserAccountColumns::PASSWORD => bcrypt('password'),
            UserAccountColumns::VERIFIED_AT => now(),
            UserAccountColumns::IS_ACTIVE => $this->faker->boolean(),
        ];
    }

    public function useUserEmail(string $email): self
    {
        return $this->state(fn (array $attributes) => [
            UserAccountColumns::EMAIL => $email,
        ]);
    }
}
