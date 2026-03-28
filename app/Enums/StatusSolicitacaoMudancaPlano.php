<?php

namespace App\Enums;

enum StatusSolicitacaoMudancaPlano: string
{
    case PENDENTE = "PENDENTE";
    case CONCLUIDO = "CONCLUIDO";
    case CANCELADO = "CANCELADO";

     public function label(): string
    {
        return match ($this) {
            self::PENDENTE => "Pendente",
            self::CONCLUIDO => "Concluído",
            self::CANCELADO => "Cancelado",
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
