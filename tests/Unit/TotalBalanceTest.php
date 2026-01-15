<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\FinancialAccount;

class TotalBalanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_total_balance_sums_accounts()
    {
        $user = User::factory()->create();

        FinancialAccount::create(["name" => "A", "type" => "AS", "balance" => 1000, "user_id" => $user->id]);
        FinancialAccount::create(["name" => "B", "type" => "AS", "balance" => 2500, "user_id" => $user->id]);

        $this->assertSame(3500, $user->totalBalance());
    }
}
