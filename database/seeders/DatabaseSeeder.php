<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// use Illuminate\Support\Facades\Schema; // HAPUS INI

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Kontrol Foreign Key sudah dipindahkan ke DemoDataSeeder.php
        
        $this->call([
            DemoDataSeeder::class, 
            // Panggil Seeder lain di sini jika ada...
        ]);
    }
}