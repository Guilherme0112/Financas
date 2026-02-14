<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use File;

class PdfService
{

    public function exportarPDF(array $lancamentos): string
    {
        $pdf = Pdf::loadView('pdfs.exportar_lancamento', [
            'dados' => $lancamentos
        ]);

        $dir = storage_path('app/private/exports/pdf');

        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $filename = 'lancamentos_' . Carbon::now()->format('Ymd_His') . '.pdf';
        $path = $dir . DIRECTORY_SEPARATOR . $filename;

        file_put_contents($path, $pdf->output());

        return $filename;
    }
}
