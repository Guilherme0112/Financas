<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:processar-faturas-do-mes')->daily();
Schedule::command('app:processar-assinaturas-expiradas')->daily();

Schedule::call(function () {
    $files = Storage::disk('local')->allFiles('exports');
    $now = time();
    $expiration = 60 * 60 * 24;

    foreach ($files as $file) {
        if ($now - Storage::disk('local')->lastModified($file) >= $expiration) {
            Storage::disk('local')->delete($file);
        }
    }
})->daily();