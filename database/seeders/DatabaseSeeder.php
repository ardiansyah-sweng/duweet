<?php

namespace Database\Seeders;

// Ambil model Transaction
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Panggil Seeder Class yang sudah ada
        $this->call([
            UserSeeder::class,
            UserAccountSeeder::class,
            AccountSeeder::class, // <-- INI PENTING, saya buka komentarnya
        ]);

        // 2. Panggil TransactionFactory buatan kamu
        //    Ini HANYA bisa berjalan jika UserAccount dan FinancialAccount sudah ada
        Transaction::factory(100)->create();
    }
}