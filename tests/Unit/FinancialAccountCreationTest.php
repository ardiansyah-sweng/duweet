<?php

namespace Tests\Unit;

use App\Models\FinancialAccount;
use App\Models\User;
use App\Models\UserFinancialAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialAccountCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_for_user_creates_fa_and_pivot(): void
    {
        $user = User::factory()->create();

        $fa = FinancialAccount::createForUser([
            'user_id' => $user->id,
            'name' => 'New Cash',
            'type' => 'AS',
            'initial_balance' => 1500,
        ]);

        $this->assertDatabaseHas('financial_accounts', [
            'id' => $fa->id,
            'name' => 'New Cash',
            'type' => 'AS',
            'is_group' => 0,
        ]);

        $this->assertDatabaseHas('user_financial_accounts', [
            'user_id' => $user->id,
            'financial_account_id' => $fa->id,
            'balance' => 1500,
            'is_active' => 1,
        ]);
    }

    public function test_asset_cannot_be_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $user = User::factory()->create();
        FinancialAccount::createForUser([
            'user_id' => $user->id,
            'name' => 'Invalid Asset',
            'type' => 'AS',
            'initial_balance' => -10,
        ]);
    }
}
