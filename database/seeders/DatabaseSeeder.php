<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test user only if not already exists
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'email' => 'test@example.com',
                'name' => 'Test User',
            ]);
        }

        // Run seeders in order: users -> user_accounts -> financial_accounts -> transactions
        $this->call([
            UserSeeder::class,
            UserAccountSeeder::class,
            FinancialAccountSeeder::class,
            TransactionSeeder::class,
        ]);

        // Calculate and display liquid assets for admin
        $this->calculateLiquidAssets();
    }

    /**
     * Calculate total liquid assets for admin
     * Menggunakan tabel user_financial_accounts sesuai PRD
     */
    private function calculateLiquidAssets(): void
    {
        // Query liquid assets dari user_financial_accounts yang aktif
        // dan terhubung ke financial_accounts dengan type AS (Asset)
        $liquidAssets = DB::table('user_financial_accounts as ufa')
            ->join('financial_accounts as fa', 'ufa.financial_account_id', '=', 'fa.id')
            ->whereIn('fa.type', ['AS']) // Asset types
            ->where('ufa.is_active', true)
            ->where('fa.is_active', true)
            ->sum('ufa.balance');

        $this->command->info("Total Liquid Assets (Admin): Rp " . number_format($liquidAssets, 0, ',', '.'));
    }
}