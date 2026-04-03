<?php

namespace Tests\Feature\Services;

use App\Services\LancamentoService;
use App\Services\XlsxService;
use Mockery;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class XlsxServiceTest extends TestCase
{
    private function criarExcelTemporario(Spreadsheet $spreadsheet): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'test_').'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);
        return $tempPath;
    }

    public function test_deve_processar_linhas_do_excel_corretamente()
    {
        $lancamentoService = Mockery::mock(LancamentoService::class);
        $lancamentoService->shouldReceive('criar')->atLeast()->once();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray(['Nome', 'Desc', 'Valor', 'Tipo', 'Mes', 'Cat', 'Pago'], null, 'A1');
        $sheet->fromArray(['Salario', 'Abril', 5000.50, 'ENTRADA', '2026-04', 'OUTROS', 1], null, 'A2');

        $tempPath = tempnam(sys_get_temp_dir(), 'test_import_').'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $service = new XlsxService($lancamentoService);

        try {
            $service->buscarXlsx($tempPath, 1);
            $this->assertTrue(true);
        } finally {
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
        }
    }

    public function test_deve_registrar_erro_quando_tipo_valor_for_invalido()
    {
        $lancamentoService = Mockery::mock(LancamentoService::class);
        // Não deve chamar o criar, pois o tipo é inválido
        $lancamentoService->shouldNotReceive('criar');

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['Nome', 'Desc', 'Valor', 'Tipo', 'Mes', 'Cat', 'Pago'], null, 'A1');
        $sheet->fromArray(['Teste Erro', 'Desc', 100, 'INVALIDO', '2026-04', 'OUTROS', 1], null, 'A2');

        $tempPath = $this->criarExcelTemporario($spreadsheet);
        $service = new XlsxService($lancamentoService);
        $service->buscarXlsx($tempPath, 1);

        $this->assertTrue(true);
        @unlink($tempPath);
    }

    public function test_deve_falhar_quando_categoria_nao_existir_no_enum()
    {
        $lancamentoService = Mockery::mock(LancamentoService::class);
        $lancamentoService->shouldNotReceive('criar');

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['Nome', 'Desc', 'Valor', 'Tipo', 'Mes', 'Cat', 'Pago'], null, 'A1');
        $sheet->fromArray(['Teste', 'Desc', 50, 'SAIDA', '2026-04', 'CATEGORIA_QUE_NAO_EXISTE', 1], null, 'A2');

        $tempPath = $this->criarExcelTemporario($spreadsheet);
        $service = new XlsxService($lancamentoService);

        $service->buscarXlsx($tempPath, 1);

        $this->assertTrue(true);
        @unlink($tempPath);
    }

    public function test_deve_ignorar_linhas_onde_o_tipo_está_vazio()
    {
        $lancamentoService = Mockery::mock(LancamentoService::class);
        $lancamentoService->shouldReceive('criar')->once();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['Nome', 'Desc', 'Valor', 'Tipo', 'Mes', 'Cat', 'Pago'], null, 'A1');
        $sheet->fromArray(['Valido', 'Desc', 100, 'ENTRADA', '2026-04', 'OUTROS', 1], null, 'A2');
        $sheet->fromArray(['', '', '', '', '', '', ''], null, 'A3'); // Linha vazia

        $tempPath = $this->criarExcelTemporario($spreadsheet);
        $service = new XlsxService($lancamentoService);

        $service->buscarXlsx($tempPath, 1);

        $this->assertTrue(true);
        @unlink($tempPath);
    }
}
