<?php

namespace App\Console\Commands;

use App\Enums\StatusPagamento;
use App\Jobs\ProcessarFaturasDoMesJob;
use App\Models\Fatura;
use App\Services\AssinaturaService;
use App\Services\MercadoPagoService;
use Illuminate\Console\Command;

class ProcessarFaturasDoMes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:processar-faturas-do-mes';

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
        ProcessarFaturasDoMesJob::dispatch();
    }
}
