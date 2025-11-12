<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class FinancialAccountDmlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_sumLiquidAssetByUser_direct()
    {
        $userId = DB::table('users')->insertGetId([
            'name' => 'Direct Test', 'email' => 'direct@example.com',
            'provinsi' => 'X', 'kabupaten' => 'Y', 'kecamatan' => 'Z', 'jalan' => 'Jl', 'kode_pos' => '12345',
            'tanggal_lahir' => 1, 'bulan_lahir' => 1, 'tahun_lahir' => 2000, 'usia' => 25,
        ]);

        $a1 = DB::table('financial_accounts')->insertGetId([ 'name' => 'A1', 'type' => 'AS', 'balance' => 100, 'initial_balance' => 100, 'is_group' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now() ]);
        $a2 = DB::table('financial_accounts')->insertGetId([ 'name' => 'A2', 'type' => 'LI', 'balance' => 50, 'initial_balance' => 50, 'is_group' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now() ]);

        DB::table('user_financial_accounts')->insert([ 'user_id' => $userId, 'financial_account_id' => $a1, 'balance' => 100, 'initial_balance' => 100, 'is_active' => true, 'created_at' => now(), 'updated_at' => now() ]);
        DB::table('user_financial_accounts')->insert([ 'user_id' => $userId, 'financial_account_id' => $a2, 'balance' => 50, 'initial_balance' => 50, 'is_active' => true, 'created_at' => now(), 'updated_at' => now() ]);

        $total = \App\Models\FinancialAccount::sumLiquidAssetByUser($userId);
        $this->assertEquals(150, $total);
    }
}
