<?php

namespace App\DTOs;

class DashboardCards
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public float $entradas,
        public float $saidas,
        public float $reserva_meta = 0.0,
        public float $total,
    )
    { }
}
