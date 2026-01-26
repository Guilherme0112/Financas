<?php

namespace App\Enums;

enum CategoriaEntrada: string
{
    case SALARIO = 'SALARIO';
    case FREELANCE = 'FREELANCE';
    case INVESTIMENTOS = 'INVESTIMENTOS';
    case BONUS = 'BONUS';
    case ALUGUEL = 'ALUGUEL';
    case REEMBOLSO = 'REEMBOLSO';
    case VENDAS = 'VENDAS';
    case OUTROS = 'OUTROS';

    public function label(): string
    {
        return match ($this) {
            self::SALARIO => 'Salário',
            self::FREELANCE => 'Freelance',
            self::INVESTIMENTOS => 'Investimentos',
            self::BONUS => 'Bônus',
            self::ALUGUEL => 'Aluguel',
            self::REEMBOLSO => 'Reembolso',
            self::VENDAS => 'Vendas',
            self::OUTROS => 'Outros',
        };
    }
}
