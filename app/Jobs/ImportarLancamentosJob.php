<?php
namespace App\Jobs;

use App\Events\ImportacaoFinalizada;
use App\Models\LancamentosImportados;
use App\Services\CsvService;
use App\Services\XlsxService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ImportarLancamentosJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 600;

    public function __construct(
        private string $path, 
        private string $tipo,
        private int $userId,
        private ?int $importacaoId = null
    ) {}

    public function handle(XlsxService $xlsxService, CsvService $csvService): void
    {
        $disk = Storage::disk('private');

        if (!$disk->exists($this->path)) {
            $this->falha("Arquivo de importação não encontrado.");
            return;
        }

        // Recupera ou cria o registro
        $importacao = LancamentosImportados::updateOrCreate(
            ['id' => $this->importacaoId],
            [
                'user_id' => $this->userId,
                'status' => 'processando',
                'tipo' => $this->tipo,
                'path' => $this->path,
                'quando_importou' => now()
            ]
        );

        try {
            $fullPath = $disk->path($this->path);
            logger()->info("Iniciando importação #{$importacao->id} - Tipo: {$this->tipo}");

            $totalLinhas = 0;

            if ($this->tipo === 'csv') {
                $file = new \SplFileObject($fullPath, 'r');
                $file->seek(PHP_INT_MAX);
                $totalLinhas = $file->key();
                
                $totalLinhas = $totalLinhas > 0 ? $totalLinhas : 0;
            } 
            
            if ($totalLinhas > 0) {
                $importacao->update(['total_linhas' => $totalLinhas]);
            }

            match ($this->tipo) {
                'xlsx', 'xls' => $xlsxService->buscarXlsx($fullPath, $this->userId), 
                'csv'         => $csvService->importar($fullPath, $this->userId),
                default       => throw new \Exception("Formato de arquivo '{$this->tipo}' não suportado.")
            };

            $importacao->update(['status' => 'concluido']);
            broadcast(new ImportacaoFinalizada($this->userId));

        } catch (\Throwable $e) {
            $mensagemErro = "Erro na importação: " . $e->getMessage();
            
            logger()->error($mensagemErro, [
                'user_id' => $this->userId,
                'path'    => $this->path
            ]);

            $importacao->update([
                'status' => 'falha',
                'erro_mensagem' => $e->getMessage()
            ]);

            broadcast(new ImportacaoFinalizada($this->userId, $mensagemErro));
            throw $e; 
        } finally {
            $disk->delete($this->path);
        }
    }

    private function falha(string $mensagem): void
    {
        logger()->error($mensagem);
        broadcast(new ImportacaoFinalizada($this->userId, $mensagem));
    }
}