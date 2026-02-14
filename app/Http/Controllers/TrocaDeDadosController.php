<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExportarXLSXRequest;
use App\Http\Requests\StoreImportarXLSXRequest;
use App\Http\Requests\StoreImportCSVRequest;
use App\Jobs\ExportarLancamentosJob;
use App\Jobs\ImportarLancamentosJob;
use App\Models\LancamentosExportados;

class TrocaDeDadosController extends Controller
{
    public function importarXLSX(StoreImportarXLSXRequest $request)
    {
        $path = $request->file('file')->store('imports/xlsx', 'private');
        ImportarLancamentosJob::dispatch($path, 'xlsx', auth()->id());
        return back()->with('success', 'Importação iniciada');
    }

    public function importarCSV(StoreImportCSVRequest $request)
    {
        $path = $request->file('file')->store('imports/csv', 'private');
        ImportarLancamentosJob::dispatch($path, 'csv', auth()->id());
        return back()->with('success', 'Importação iniciada');
    }

    public function exportarXLSX(StoreExportarXLSXRequest $request)
    {
        $data = $request->validated();
        $data['tipo_arquivo'] = 'xlsx';
        ExportarLancamentosJob::dispatch(
            $data,
            auth()->id()
        );
        return back()->with('success', 'Exportação iniciada');
    }

    public function exportarPDF(StoreExportarXLSXRequest $request)
    {
        $data = $request->validated();
        $data['tipo_arquivo'] = 'pdf';
        ExportarLancamentosJob::dispatch(
            $data,
            auth()->id()
        );
        return back()->with('success', 'Exportação iniciada');
    }

    public function download(string $id)
    {
        $export = LancamentosExportados::findOrFail($id);
        abort_unless($export->user_id === auth()->id(), 403);
        abort_unless($export->status === 'concluido', 400);

        return response()->download(
            storage_path("app/private/exports/{$export->tipo}/{$export->filename}")
        );
    }
}
