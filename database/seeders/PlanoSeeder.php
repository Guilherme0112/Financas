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
        Plano::updateOrCreate(
            ['plano' => Planos::BASICO],
            [
                'nome' => 'Plano Básico',
                'descricao' => 'Acesso limitado a recursos básicos.',
                'preco' => 20.00,
            ]
        );

        Plano::updateOrCreate(
            ['plano' => Planos::GRATUITO],
            [
                'nome' => 'Plano Gratuito',
                'descricao' => 'Teste grátis por 7 dias',
                'preco' => 0.00,
            ]
        );
    }
}
