<?php

namespace Database\Factories;

use App\Models\Transaksi;
use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class TransaksiFactory extends Factory
{
    protected $model = Transaksi::class;

    public function definition(): array
    {
        // Get random leaf account (is_group = false) that can have transactions
        $leafAccount = DB::table('accounts')
            ->where('is_group', false)
            ->inRandomOrder()
            ->first();

        return [
            'account_id' => $leafAccount ? $leafAccount->id : 1,
            'user_id' => 1, // Adjust if you have multiple users
            'date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'description' => $this->faker->randomElement([
                'Pembelian groceries',
                'Transfer ke tabungan',
                'Bayar tagihan listrik',
                'Gaji bulanan',
                'Top up e-wallet',
                'Belanja online',
                'Makan di restoran',
                'Isi bensin',
                'Bayar cicilan',
                'Investasi saham',
                'Dividen investasi',
                'Freelance project',
                'Bonus tahunan',
                'Shopping mall',
                'Transportasi umum',
            ]),
            'amount' => $this->faker->numberBetween(10000, 5000000),
            'type' => $this->faker->randomElement(['debit', 'credit']),
            'meta' => [
                'category' => $this->faker->randomElement(['food', 'transport', 'utility', 'income', 'investment']),
                'notes' => $this->faker->optional()->sentence(),
            ],
        ];
    }
}
