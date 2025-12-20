<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Account;
use App\Models\User;

class TestNestedAccount extends Command
{
    protected $signature = 'test:nested-account';
    protected $description = 'Menampilkan struktur nested account user';

    public function handle()
    {
        // Misalnya kita ambil user pertama
        $user = User::first();

        if (!$user) {
            $this->error("Tidak ada user ditemukan!");
            return;
        }

        // Ambil semua akun user dengan relasi child
        $accounts = Account::where('user_id', $user->id)
            ->whereNull('parent_id')
            ->with('children')
            ->get();

        $this->info("Struktur Akun untuk User: " . $user->name);
        $this->line(json_encode($accounts, JSON_PRETTY_PRINT));
    }
}
