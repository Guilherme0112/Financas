<?php

namespace App\DTOs;

use Illuminate\Pagination\LengthAwarePaginator;

class Dashboard
{

    public function __construct(
        public DashboardCards $cards,
        public array $graficos,
        public array $porcentual,
        public array $lancamentos_perto_de_vencer,
        public array $lancamentos_vencidos,
        public LengthAwarePaginator $limites,
        public LengthAwarePaginator $metas
    )
    { }
}
