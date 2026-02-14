<?php

namespace App\Services;

use App\Models\Lancamento;
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
    public function buscarXlsx(string $path): Lancamento
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        return $this->importarParaLancamentos($sheet);
    }

    public function importarParaLancamentos(Worksheet $sheet): Lancamento
    {
        $rows = $sheet->toArray(null, true, true, true);

        // remove cabeçalho
        unset($rows[1]);

        foreach ($rows as $row) {
            if (empty(trim($row['D'] ?? ''))) continue;
            
            $tipo = TipoValor::tryFrom(strtoupper(trim($row['D'])));
            if (!$tipo) continue;

            // TODO: chamar service para salvar
            return Lancamento::create([
                'nome' => trim($row['A']),
                'descricao' => trim($row['B']),
                'valor' => (float) $row['C'],
                'tipo' => $tipo,
                'mes_referencia' => $row['E'],
                'foi_pago' => (bool) $row['G'],
                'categoria_entrada' => $tipo === TipoValor::ENTRADA
                    ? CategoriaEntrada::from(strtoupper(trim($row['F'])))
                    : null,
                'categoria_saida' => $tipo === TipoValor::SAIDA
                    ? CategoriaSaida::from(strtoupper(trim($row['F'])))
                    : null,
            ]);
        }
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
