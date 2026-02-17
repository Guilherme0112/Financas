<?php

namespace App\Jobs;

use App\Events\ImportacaoFinalizada;
use App\Repositories\LancamentosExportadosRepository;
use App\Services\CsvService;
use App\Services\XlsxService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ImportarLancamentosJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $path, 
        private string $tipo = 'xlsx',
        private string $userId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(XlsxService $xlsxService, CsvService $csvService): void
    {
        try {
            if (!Storage::disk('private')->exists($this->path)) {
                logger()->error('Arquivo não encontrado no disk private', [
                    'path' => $this->path,
                ]);
                return;
            }

            if ($this->tipo === 'xlsx') {
                $fullPath = Storage::disk('private')->path($this->path);
                $xlsxService->buscarXlsx($fullPath, $this->userId);
            } 
            if ($this->tipo === 'csv') {
                $fullPath = Storage::disk('private')->path($this->path);
                $csvService->importar($fullPath, $this->userId);
            }
            broadcast(new ImportacaoFinalizada($this->userId));
        } catch (\Throwable $e) {
            logger()->error('Erro na exportação: ' . $e->getMessage(), [
                'user_id' => $this->userId,
                'tipo' => $this->tipo,
                'path' => $this->path
            ]);
            broadcast(new ImportacaoFinalizada($this->userId, "Ocorreu um erro durante a importação: " . $e->getMessage()));
        } finally {
            Storage::disk('private')->delete($this->path);
        }
    }
}