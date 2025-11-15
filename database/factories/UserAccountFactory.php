<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
<<<<<<< HEAD
=======
use Illuminate\Support\Str;
use App\Constants\UserAccountColumns;
>>>>>>> f69f6a334e79a0c91b6090aee470fb63b59926ce

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAccount>
 */
class UserAccountFactory extends Factory
{
<<<<<<< HEAD
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }
=======
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
>>>>>>> f69f6a334e79a0c91b6090aee470fb63b59926ce
}
