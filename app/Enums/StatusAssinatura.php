<?php

namespace App\Enums;

enum StatusAssinatura: string
{
    case ATIVA = 'ATIVA';
    case CANCELADA = 'CANCELADA';
    case EXPIRADA = 'EXPIRADA';
    case PENDENTE = 'PENDENTE';
    case SUSPENSA = 'SUSPENSA';

    public function label()
    {
        return [
            self::ATIVA->value => 'Ativa',
            self::CANCELADA->value => 'Cancelada',
            self::EXPIRADA->value => 'Expirada',
            self::PENDENTE->value => 'Pendente',
            self::SUSPENSA->value => 'Suspensa',
        ];
    }

    public static function options()
    {
        return array_map(fn($c) => [
            'value' => $c->value,
            'label' => $c->label(),
        ], self::cases());
    }
}
