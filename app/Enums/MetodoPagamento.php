<?php

namespace App\Enums;

enum MetodoPagamento: string
{
    case PIX = "PIX";
    case CARTAO_CREDITO = "CARTAO_CREDITO";
    case BOLETO = "BOLETO";

    public function label()
    {
        return match ($this) {
            self::PIX => "Pix",
            self::CARTAO_CREDITO => "Cartão de Crédito",
            self::BOLETO => "Boleto",
        };
    }

    public static function options()
    {
        return array_map(fn($c) => [
            'value' => $c->value,
            'label' => $c->label(),
        ], self::cases());
    }

    public static function deMercadoPago(string $typeId, string $methodId): self
    {
        return match ($typeId) {
            'credit_card', 'debit_card', 'prepaid_card' => self::CARTAO_CREDITO,
            'ticket' => self::BOLETO,
            'bank_transfer' => ($methodId === 'pix') ? self::PIX : self::BOLETO,
            default => self::CARTAO_CREDITO,
        };
    }
}
