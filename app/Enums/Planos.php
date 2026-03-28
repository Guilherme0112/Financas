<?php

namespace App\Enums;

enum Planos: string
{
    case GRATUITO = 'GRATUITO';
    case BASICO = 'BASICO';

    public function label(): string
    {
        return match ($this) {
            self::GRATUITO => 'Gratuito',
            self::BASICO => 'Básico',
        };
    }

    public static function options(): array
    {
        return array_map(fn($plano) => [
            'value' => $plano->value,
            'label' => $plano->label(),
        ], self::cases());
    }
}   
