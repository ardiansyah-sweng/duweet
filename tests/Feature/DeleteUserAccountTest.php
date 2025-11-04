<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserAccount;
use App\Constants\UserAccountColumns;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteUserAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_user_account_via_api()
    {
        $user = User::factory()->create();

        $ua = UserAccount::create([
            UserAccountColumns::ID_USER => $user->id,
            UserAccountColumns::USERNAME => 'testuser',
            UserAccountColumns::EMAIL => 'testuser@example.com',
            UserAccountColumns::PASSWORD => bcrypt('password'),
            UserAccountColumns::IS_ACTIVE => true,
        ]);

        $this->assertDatabaseHas('user_accounts', ['id' => $ua->id]);

        $response = $this->deleteJson(route('api.user-account.destroy', ['id' => $ua->id]));

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseMissing('user_accounts', ['id' => $ua->id]);
    }

    public function test_delete_user_account_raw_via_api()
    {
        $user = User::factory()->create();

        $ua = UserAccount::create([
            UserAccountColumns::ID_USER => $user->id,
            UserAccountColumns::USERNAME => 'testuser2',
            UserAccountColumns::EMAIL => 'testuser2@example.com',
            UserAccountColumns::PASSWORD => bcrypt('password'),
            UserAccountColumns::IS_ACTIVE => true,
        ]);

        $this->assertDatabaseHas('user_accounts', ['id' => $ua->id]);

        $response = $this->deleteJson(route('api.user-account.destroy-raw', ['id' => $ua->id]));

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseMissing('user_accounts', ['id' => $ua->id]);
    }
}
