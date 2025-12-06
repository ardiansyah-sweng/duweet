<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            UserAccountSeeder::class,
<<<<<<< HEAD
            AccountSeeder::class,
            TransaksiSeeder::class,
=======
            //AccountSeeder::class,
>>>>>>> f69f6a334e79a0c91b6090aee470fb63b59926ce
        ]);
    }
}
