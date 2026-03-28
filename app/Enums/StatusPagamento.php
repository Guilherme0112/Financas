<?php

namespace App\Enums;

enum StatusPagamento: string
{
    case PENDENTE = 'PENDENTE';
    case PROCESSANDO = 'PROCESSANDO';
    case APROVADO = 'APROVADO';
    case CANCELADO = 'CANCELADO';
    case REEMBOLSADO = 'REEMBOLSADO';

    public function label()
    {
        return match ($this) {
            self::PENDENTE => 'Pendente',
            self::PROCESSANDO => 'Processando',
            self::APROVADO => 'Aprovado',
            self::CANCELADO => 'Cancelado',
            self::REEMBOLSADO => 'Reembolsado',
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
