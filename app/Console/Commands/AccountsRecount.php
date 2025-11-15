<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AccountsRecount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:recount {--chunk=100 : Number of users to process per chunk}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate and populate users.accounts_count from user_accounts';

    public function handle()
    {
        $chunkSize = (int) $this->option('chunk');

        $this->info('Counting user_accounts grouped by id_user...');
        $counts = DB::table('user_accounts')
            ->select('id_user', DB::raw('COUNT(*) as cnt'))
            ->groupBy('id_user')
            ->pluck('cnt', 'id_user');

        $this->info('Recounting users and updating accounts_count...');

        $bar = $this->output->createProgressBar(User::count());
        $bar->start();

        User::chunk($chunkSize, function ($users) use ($counts, $bar) {
            foreach ($users as $user) {
                $new = $counts[$user->id] ?? 0;
                // Use query builder to avoid firing model events and to be faster
                DB::table('users')->where('id', $user->id)->update(['accounts_count' => $new]);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info('Done.');
        return 0;
    }
}
