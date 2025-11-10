<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\SampleTotalsSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Delegate to the SampleTotalsSeeder which creates 4 users and their accounts
        $this->call([SampleTotalsSeeder::class]);
    }
}
