<?php

namespace Database\Factories;
use App\Models\UserAccount;
use App\Models\User;
use App\Constants\UserAccountColumns;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAccount>
 */
class UserAccountFactory extends Factory
{
    protected $model = UserAccount::class;

    public function definition(): array
    {
        return [
            UserAccountColumns::USERNAME => $this->faker->unique()->userName(),
            UserAccountColumns::EMAIL => $this->faker->unique()->safeEmail(),
            UserAccountColumns::PASSWORD => bcrypt('123456'), // password default
            UserAccountColumns::VERIFIED_AT => now(),
            UserAccountColumns::IS_ACTIVE => true,
        ];
    }
}
