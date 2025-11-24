<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('transactions')->insert([
            [
                'type' => 'income',
                'description' => 'Iuran kas minggu 1',
                'amount' => 20000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'expense',
                'description' => 'Beli spidol kelas',
                'amount' => 10000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
