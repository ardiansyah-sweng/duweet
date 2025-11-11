<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DebugPNBN extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:pnbn';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug PNBN calculations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 PNBN Debug Analysis');
        $this->newLine();

        // 1. Check Financial Accounts
        $this->info('1. PNBN Financial Accounts:');
        $pnbnAccounts = DB::table('financial_accounts')
            ->where('name', 'Saham PNBN')
            ->select('id', 'name', 'initial_balance', 'balance', 'stock_symbol')
            ->get();

        foreach ($pnbnAccounts as $acc) {
            $this->line("   ID: {$acc->id} | {$acc->name} | Symbol: {$acc->stock_symbol}");
            $this->line("   Initial: Rp " . number_format($acc->initial_balance));
            $this->line("   Current: Rp " . number_format($acc->balance));
            $this->newLine();
        }

        // 2. Check Assets
        $this->info('2. PNBN Assets:');
        $pnbnAssets = DB::table('assets as a')
            ->join('financial_accounts as fa', 'a.financial_account_id', '=', 'fa.id')
            ->where('fa.name', 'Saham PNBN')
            ->select([
                'a.id as asset_id',
                'a.financial_account_id',
                'a.buy_quantity',
                'a.bought_price',
                'a.acquisition_date',
                'fa.name as account_name'
            ])
            ->get();

        foreach ($pnbnAssets as $asset) {
            $value = $asset->buy_quantity * $asset->bought_price;
            $this->line("   Asset ID: {$asset->asset_id} | Account: {$asset->financial_account_id}");
            $this->line("   Quantity: {$asset->buy_quantity} lots");
            $this->line("   Price: Rp " . number_format($asset->bought_price) . " per lot");
            $this->line("   Base Value: Rp " . number_format($value));
            $this->line("   Date: {$asset->acquisition_date}");
            $this->newLine();
        }

        // 3. Manual Calculation
        $this->info('3. Manual Calculation:');
        $expectedValue = 35 * 1678 * 100; // 35 lots × 1678 per lot × 100 shares per lot
        $this->line("   Expected: 35 lots × Rp 1,678 × 100 = Rp " . number_format($expectedValue));

        // 4. Check if there's data mismatch in AssetSeeder
        $this->info('4. Check AssetSeeder Data Issues:');
        $allPnbnAccounts = DB::table('financial_accounts')
            ->where('name', 'LIKE', '%PNBN%')
            ->get();
        
        $this->info("   Total PNBN-related accounts: " . $allPnbnAccounts->count());
        foreach ($allPnbnAccounts as $acc) {
            $this->line("   ID: {$acc->id} | {$acc->name} | Balance: " . number_format($acc->balance));
        }

        return 0;
    }
}
