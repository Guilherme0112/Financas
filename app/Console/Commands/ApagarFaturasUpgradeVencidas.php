<?php

namespace App\Console\Commands;

use App\Jobs\ApagarFaturasUpgradeVencidasJob;
use Illuminate\Console\Command;

class ApagarFaturasUpgradeVencidas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:apagar-faturas-upgrade-vencidas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ApagarFaturasUpgradeVencidasJob::dispatch();
    }
}
