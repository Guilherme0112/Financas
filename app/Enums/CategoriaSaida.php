<?php

namespace App\Enums;

enum CategoriaSaida: string
{
    case ALIMENTACAO = 'ALIMENTACAO';
    case MORADIA = 'MORADIA';
    case UTILIDADES = "UTILIDADES";
    case TRANSPORTE = 'TRANSPORTE';
    case SAUDE = 'SAUDE';
    case EDUCACAO = 'EDUCACAO';
    case LAZER = 'LAZER';
    case ASSINATURAS = 'ASSINATURAS';
    case IMPOSTOS = 'IMPOSTOS';
    case VESTUARIO = 'VESTUARIO';
    case DOACOES = "DOACOES";
    case OUTROS = 'OUTROS';

    public function label(): string
    {
        return match ($this) {
            self::ALIMENTACAO => 'Alimentação',
            self::MORADIA => 'Moradia',
            self::TRANSPORTE => 'Transporte',
            self::SAUDE => 'Saúde',
            self::UTILIDADES => "Utilidades",
            self::EDUCACAO => 'Educação',
            self::LAZER => 'Lazer',
            self::ASSINATURAS => 'Assinaturas',
            self::IMPOSTOS => 'Impostos',
            self::VESTUARIO => 'Vestuário',
            self::DOACOES => "Doações",
            self::OUTROS => 'Outros',
        };
    }

    public static function options()
    {
        return array_map(fn($c) => [
            'value' => $c->value,
            'label' => $c->label(),
        ], self::cases());
    }
}
