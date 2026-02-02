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
        public float $total
    )
    { }
}
