<?php

namespace Tests\Feature\Services;

use App\Enums\TipoValor;
use App\Models\User;
use App\Services\CsvService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Storage;
use Tests\TestCase;

class CsvServiceTest extends TestCase
{
    
    use RefreshDatabase;

    public function test_deve_importar_csv_corretamente()
    {
        Storage::fake('private');
        
        $user = User::factory()->create();
        $userId = $user->id;

        $content = "Salario,Mes Abril,1500.50,ENTRADA,2026-04-01,OUTROS,1";
        
        $filename = 'teste.csv';
        Storage::disk('private')->put($filename, $content);
        $fullPath = Storage::disk('private')->path($filename);

        $service = new CsvService();
        $service->importar($fullPath, $userId);

        $this->assertDatabaseHas('lancamentos', [
            'nome' => 'Salario',
            'valor' => 1500.50,
            'tipo' => TipoValor::ENTRADA, 
            'user_id' => $userId,
            'foi_pago' => true
        ]);
    }
}
