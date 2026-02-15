<?php

namespace App\Jobs;

use App\Events\ExportacaoFinalizada;
use App\Models\LancamentosExportados;
use App\Services\LancamentoService;
use App\Services\PdfService;
use App\Services\XlsxService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExportarLancamentosJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private array $request, private int $userId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(LancamentoService $lancamentoService, XlsxService $xlsxService, PdfService $pdfService): void
    {
        try {
            logger()->info('Iniciando exportação de lançamentos', [
                'user_id' => $this->userId,
                'request' => $this->request
            ]);
            
            $dados = $lancamentoService->listar((array) $this->request, 999, $this->userId);
            if ($dados->isEmpty()) {
                broadcast(new ExportacaoFinalizada(
                    $this->userId,
                    null,
                    'Nenhum lançamento encontrado para os filtros selecionados.'
                ));
                return;
            }

            $filename = null;
            if($this->request['tipo_arquivo'] === 'pdf') {
                $filename = $pdfService->exportarPDF($dados->items());
            } else {
                $filename = $xlsxService->exportarXlsx($dados->items());
            }

            $lancamentosExportado = LancamentosExportados::create([
                'user_id' => $this->userId,
                'status' => 'concluido',
                'tipo' => $this->request['tipo_arquivo'],
                'filename' => $filename
            ]);
            broadcast(new ExportacaoFinalizada($this->userId, $lancamentosExportado->id));
        } catch (\Throwable $e) {
            logger()->error('Erro na exportação: ' . $e->getMessage(), [
                'user_id' => $this->userId,
                'request' => $this->request
            ]);
            broadcast(new ExportacaoFinalizada(
                $this->userId,
                null,
                'Ocorreu um erro durante a exportação: ' . $e->getMessage()
            ));
        }
    }
}
