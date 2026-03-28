<?php

namespace App\Jobs;

use App\Enums\StatusAssinatura;
use App\Models\Assinatura;
use DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessarAssinaturasExpiradasJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle()
    {
        $assinaturasVencidas = Assinatura::query()
            ->where('status', StatusAssinatura::ATIVA)
            ->where('data_fim', '<', now())
            ->with(['user'])
            ->get();

        if ($assinaturasVencidas->isEmpty()) {
            logger()->info("Nenhuma assinatura vencida para processar.");
            return;
        }

        foreach ($assinaturasVencidas as $assinatura) {
            DB::transaction(function () use ($assinatura) {
                $assinatura->update([
                    'status' => StatusAssinatura::EXPIRADA
                ]);
                $assinatura->user->update(['is_active' => false]);
                logger()->alert("Assinatura ID {$assinatura->id} do usuário {$assinatura->user->name} foi EXPIRADA.");
            });
        }

        logger()->info("Verificação de expiração concluída.");
    }
}
