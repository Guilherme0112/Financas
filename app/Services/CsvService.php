<?php

namespace App\Services;

use App\Models\Lancamento;
use App\Enums\TipoValor;
use App\Enums\CategoriaEntrada;
use App\Enums\CategoriaSaida;

class CsvService
{
    public function importar(string $path): void
    {
        if (!file_exists($path)) {
            throw new \RuntimeException('Arquivo CSV não encontrado');
        }

        $handle = fopen($path, 'r');

        if (!$handle) {
            throw new \RuntimeException('Não foi possível abrir o CSV');
        }

        $header = fgetcsv($handle, 0, ',');

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $this->processarLinha($row);
        }

        fclose($handle);
    }

    private function processarLinha(array $row): void
    {

        if (empty(trim($row[3] ?? ''))) {
            return;
        }

        $tipo = TipoValor::tryFrom(strtoupper(trim($row[3])));
        if (!$tipo) {
            return;
        }

        // todo: use service e na service usar o db::collection
        Lancamento::create([
            'nome' => trim($row[0]),
            'descricao' => trim($row[1]),
            'valor' => (float) str_replace(',', '.', $row[2]),
            'tipo' => $tipo,
            'mes_referencia' => $row[4],
            'foi_pago' => (bool) $row[6],
            'categoria_entrada' => $tipo === TipoValor::ENTRADA
                ? CategoriaEntrada::from(strtoupper(trim($row[5])))
                : null,
            'categoria_saida' => $tipo === TipoValor::SAIDA
                ? CategoriaSaida::from(strtoupper(trim($row[5])))
                : null,
        ]);
    }
}
