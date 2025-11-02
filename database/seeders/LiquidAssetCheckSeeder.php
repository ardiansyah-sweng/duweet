<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class LiquidAssetCheckSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info("=== Cek Total Liquid Asset per User ===");

        $users = User::query()->get();

        if ($users->isEmpty()) {
            $this->command->warn("❗ Tidak ada user. Jalankan seeder data dulu atau migrate:fresh --seed.");
            return;
        }

        $rows = $users->map(function ($u) {
            return [
                'User ID'             => $u->id,
                'Nama'                => $u->name,
                'Total Liquid Asset'  => number_format($u->totalLiquidAsset(), 0, ',', '.'),
            ];
        })->toArray();

        $this->command->table(['User ID', 'Nama', 'Total Liquid Asset'], $rows);
        $this->command->info("✅ Query sum liquid asset selesai.");
    }
}
