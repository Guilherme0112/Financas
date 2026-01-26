<?php

namespace App\Enums;

enum CategoriaSaida: string
{
    case ALIMENTACAO = 'ALIMENTACAO';
    case MORADIA = 'MORADIA';
    case TRANSPORTE = 'TRANSPORTE';
    case SAUDE = 'SAUDE';
    case EDUCACAO = 'EDUCACAO';
    case LAZER = 'LAZER';
    case ASSINATURAS = 'ASSINATURAS';
    case IMPOSTOS = 'IMPOSTOS';
    case VESTUARIO = 'VESTUARIO';
    case OUTROS = 'OUTROS';

    public function label(): string
    {
        return match ($this) {
            self::ALIMENTACAO => 'Alimentação',
            self::MORADIA => 'Moradia',
            self::TRANSPORTE => 'Transporte',
            self::SAUDE => 'Saúde',
            self::EDUCACAO => 'Educação',
            self::LAZER => 'Lazer',
            self::ASSINATURAS => 'Assinaturas',
            self::IMPOSTOS => 'Impostos',
            self::VESTUARIO => 'Vestuário',
            self::OUTROS => 'Outros',
        };
    }
}
