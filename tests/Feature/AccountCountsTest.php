<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\FinancialAccount;
use App\Models\UserFinancialAccount;

class AccountCountsTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_counts_endpoint_returns_expected_structure()
    {
        // Create users
        $userA = User::factory()->create(['name' => 'User A']);
        $userB = User::factory()->create(['name' => 'User B']);

        // Create user accounts
        UserAccount::factory()->create([ 'id_user' => $userA->id ]);
        UserAccount::factory()->create([ 'id_user' => $userA->id ]);
        UserAccount::factory()->create([ 'id_user' => $userB->id ]);

        // Create financial accounts
        $fa1 = FinancialAccount::create([
            'parent_id' => null,
            'name' => 'Test FA 1',
            'type' => 'AS',
            'balance' => 1000,
            'initial_balance' => 1000,
            'description' => 'desc',
            'is_group' => false,
            'is_active' => true,
            'is_liquid' => true,
            'sort_order' => 1,
            'level' => 0,
        ]);

        // Assign financial accounts to user accounts
        $uaA = UserAccount::where('id_user', $userA->id)->first();
        $uaB = UserAccount::where('id_user', $userB->id)->first();

        UserFinancialAccount::create([
            'user_id' => $userA->id,
            'user_account_id' => $uaA->id,
            'financial_account_id' => $fa1->id,
            'initial_balance' => 500,
            'balance' => 500,
            'is_active' => true,
        ]);

        UserFinancialAccount::create([
            'user_id' => $userB->id,
            'user_account_id' => $uaB->id,
            'financial_account_id' => $fa1->id,
            'initial_balance' => 200,
            'balance' => 200,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/reports/account-counts');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            ['user_id', 'name', 'user_account_count', 'financial_account_count']
        ]);

        $data = collect($response->json());

        $this->assertTrue($data->contains(function ($item) use ($userA) {
            return $item['user_id'] === $userA->id && $item['user_account_count'] === 2 && $item['financial_account_count'] === 1;
        }));

        $this->assertTrue($data->contains(function ($item) use ($userB) {
            return $item['user_id'] === $userB->id && $item['user_account_count'] === 1 && $item['financial_account_count'] === 1;
        }));
    }
}
