<?php

namespace App\Services;

use App\Services\LancamentoService;
use App\Enums\TipoValor;
use App\Enums\CategoriaEntrada;
use App\Enums\CategoriaSaida;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class XlsxService
{
    public function __construct(
        private LancamentoService $lancamentoService
    ) {
    }
    public function buscarXlsx(string $path, int $userId): void
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $resultado = $this->importarParaLancamentos($sheet, $userId);
        logger()->info('Importação XLSX concluída', [
            'user_id' => $userId,
            'importados' => $resultado['importados'],
            'erros' => count($resultado['erros'])
        ]);
        if (!empty($resultado['erros'])) {
            logger()->warning('Erros na importação XLSX', [
                'user_id' => $userId,
                'erros' => $resultado['erros']
            ]);
        }
    }

    public function importarParaLancamentos(Worksheet $sheet, int $userId)
    {
        $rows = $sheet->toArray(null, true, true, true);
        // remove cabeçalho
        unset($rows[1]);

        $importados = 0;
        $erros = [];

        foreach ($rows as $rowIndex => $row) {
            if (empty(trim($row['D'] ?? ''))) continue;
            
            try {
                $tipo = TipoValor::tryFrom(strtoupper(trim($row['D'])));
                if (!$tipo) {
                    $erros[] = "Linha {$rowIndex}: Tipo inválido";
                    continue;
                }

                $dados = [
                    'nome' => trim($row['A']),
                    'descricao' => trim($row['B']),
                    'valor' => (float) $row['C'],
                    'tipo' => $tipo->value,
                    'mes_referencia' => $row['E'],
                    'foi_pago' => (bool) $row['G'],
                    'recorrente' => false,
                ];

                if ($tipo === TipoValor::ENTRADA) {
                    $categoria = CategoriaEntrada::tryFrom(strtoupper(trim($row['F'])));
                    if (!$categoria) {
                        $erros[] = "Linha {$rowIndex}: Categoria de entrada inválida";
                        continue;
                    }
                    $dados['categoria_entrada'] = $categoria->value;
                } else {
                    $categoria = CategoriaSaida::tryFrom(strtoupper(trim($row['F'])));
                    if (!$categoria) {
                        $erros[] = "Linha {$rowIndex}: Categoria de saída inválida";
                        continue;
                    }
                    $dados['categoria_saida'] = $categoria->value;
                }

                $this->lancamentoService->criar($userId, $dados);
                $importados++;
            } catch (\Throwable $e) {
                $erros[] = "Linha {$rowIndex}: " . $e->getMessage();
            }
        }

        return [
            'importados' => $importados,
            'erros' => $erros
        ];
    }

    public function exportarXlsx(array $lancamentos): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            'Nome',
            'Descrição',
            'Valor',
            'Tipo',
            "Mês de Referência",
            'Categoria',
            'Foi Pago',
        ], null, 'A1');

        $dados = collect($lancamentos)->map(fn($l) => [
            $l->nome,
            $l->descricao,
            $l->valor,
            $l->tipo->value,
            $l->mes_referencia,
            $l->categoria_entrada?->value ?? $l->categoria_saida?->value,
            $l->foi_pago ? 'Sim' : 'Não',
        ])->toArray();

        $sheet->fromArray($dados, null, 'A2');

        $dir = storage_path('app/private/exports/xlsx');

        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $filename = 'lancamentos_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        $path = $dir . '/' . $filename;

        (new Xlsx($spreadsheet))->save($path);

        return $filename;
    }
}
