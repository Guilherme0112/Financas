<?php

namespace App\Enums;

enum TipoValor: string
{
    case ENTRADA = 'ENTRADA';
    case SAIDA = 'SAIDA';
    case RESERVA_META = "RESERVA_META";
    case RESERVA_EMERGENCIA = "RESERVA_EMERGENCIA";

    public function label(): string
    {
        return match ($this) {
            self::ENTRADA => "Entrada",
            self::SAIDA => "Saída",
            self::RESERVA_EMERGENCIA => 'Reserva de Emergência',
            self::RESERVA_META => "Reserva para Meta",
        };
    }
}
