<?php

namespace Tests\Feature\Jobs;

use Tests\TestCase;
use App\Jobs\ImportarLancamentosJob;
use App\Services\XlsxService;
use App\Services\CsvService;
use App\Events\ImportacaoFinalizada;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Event; // Importe o Event
use Mockery;

class ImportarLancamentosJobTest extends TestCase
{
    public function test_deve_chamar_xlsx_service_e_deletar_arquivo()
    {
        Storage::fake('private');
        Event::fake(); // Use Event em vez de Broadcast

        $userId = 1;
        $path = 'imports/teste.xlsx';
        Storage::disk('private')->put($path, 'conteudo_fake');

        $xlsxService = Mockery::mock(XlsxService::class);
        $csvService = Mockery::mock(CsvService::class);

        $xlsxService->shouldReceive('buscarXlsx')
            ->once()
            ->with(Storage::disk('private')->path($path), (string) $userId);

        $job = new ImportarLancamentosJob($path, 'xlsx', $userId);
        $job->handle($xlsxService, $csvService);

        // Asserts
        Storage::disk('private')->assertMissing($path);
        Event::assertDispatched(ImportacaoFinalizada::class);
    }
}