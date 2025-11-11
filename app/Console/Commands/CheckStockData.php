<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckStockData extends Command
{
    protected $signature = 'check:stock-data';
    protected $description = 'Check stock data in database';

    public function handle()
    {
        $this->info('🔍 Checking Stock Data in Database...');
        
        // Check stock-related accounts
        $stockAccounts = DB::table('financial_accounts')
            ->where('name', 'like', '%saham%')
            ->orWhere('name', 'like', '%PNBN%')
            ->orWhere('name', 'like', '%BBRI%')
            ->orWhere('name', 'like', '%KEEN%')
            ->get(['id', 'name', 'stock_symbol']);

        $this->line('Stock-related accounts:');
        foreach ($stockAccounts as $acc) {
            $this->line("  {$acc->id}: {$acc->name} - Symbol: " . ($acc->stock_symbol ?: 'NULL'));
        }

        // Check assets with stock accounts
        $stockAssets = DB::table('assets as a')
            ->join('financial_accounts as fa', 'a.financial_account_id', '=', 'fa.id')
            ->where('fa.name', 'like', '%saham%')
            ->get(['a.id', 'a.financial_account_id', 'a.buy_quantity', 'fa.name']);

        $this->line('');
        $this->line('Stock assets:');
        foreach ($stockAssets as $asset) {
            $this->line("  Asset {$asset->id}: {$asset->name} - Qty: {$asset->buy_quantity}");
        }
        
        return 0;
    }
}
