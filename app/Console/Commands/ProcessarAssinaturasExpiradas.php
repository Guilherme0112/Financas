<?php

namespace App\Console\Commands;

use App\Jobs\ProcessarAssinaturasExpiradasJob;
use Illuminate\Console\Command;

class ProcessarAssinaturasExpiradas extends Command
{
    /**
     * Execute the console command.
     */
    protected $signature = 'app:processar-assinaturas-expiradas';
    protected $description = 'Verifica assinaturas com prazo de cobrança vencido e as marca como expiradas';

    public function handle()
    {
        ProcessarAssinaturasExpiradasJob::dispatch();
    }
}