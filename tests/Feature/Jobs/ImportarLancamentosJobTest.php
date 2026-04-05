<?php

namespace Tests\Feature\Jobs;

use App\Events\ImportacaoFinalizada;
use App\Jobs\ImportarLancamentosJob;
use App\Models\User;
use App\Services\CsvService;
use App\Services\XlsxService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class ImportarLancamentosJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_deve_chamar_xlsx_service_e_deletar_arquivo()
    {
        Storage::fake('private');
        Event::fake();

        $user = User::factory()->create();
        $userId = $user->id;
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
