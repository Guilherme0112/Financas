<?php

namespace App\Enums;

enum TipoCobranca: string
{
    case CICLO_NORMAL = "CICLO_NORMAL";
    case UPGRADE = "UPGRADE";
    case ESTORNO = "ESTORNO";

    public function label(): string
    {
        return match ($this) {
            self::CICLO_NORMAL => "Ciclo Normal",
            self::UPGRADE => "Upgrade",
            self::ESTORNO => "Estorno",
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
