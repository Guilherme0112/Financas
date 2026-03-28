<?php

namespace Database\Seeders;

use App\Enums\Planos;
use App\Models\Plano;
use Illuminate\Database\Seeder;

class PlanoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plano::create([
            'nome' => 'Plano Básico',
            "plano" => Planos::BASICO,
            'descricao' => 'Acesso limitado a recursos básicos.',
            'preco' => 20.00,
        ]);
        
        Plano::create([
            'nome' => 'Plano Gratuito',
            "plano" => Planos::GRATUITO,
            'descricao' => 'Teste grátis por 7 dias',
            'preco' => 0.00,
        ]);
    }
}
