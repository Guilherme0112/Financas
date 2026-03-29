<?php

namespace App\Models;

use App\Enums\MetodoPagamento;
use App\Enums\StatusPagamento;
use App\Enums\TipoCobranca;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fatura extends Model
{

    use HasFactory;

    protected $appends = ["metodo_pagamento_label"];

    protected $fillable = [
        'user_id',
        'assinatura_id',
        'valor',
        'status',
        'metodo_pagamento',
        'vencimento_em',
        'periodo_inicio',
        'periodo_fim',
        'referencia_externa',
        'url_pagamento',
        'pago_em',
        'tipo_cobranca'
    ];

    protected $casts = [
        'pago_em' => 'datetime',
        'vencimento_em' => 'datetime',
        'valor' => 'decimal:2',
        'metodo_pagamento' => MetodoPagamento::class,
        "status" => StatusPagamento::class,
        'tipo_cobranca' => TipoCobranca::class
    ];

 
    public function getMetodoPagamentoLabelAttribute()
    {
        return $this->metodo_pagamento?->label() ?? 'Aguardando Pagamento';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assinatura()
    {
        return $this->belongsTo(Assinatura::class);
    }

    public function solicitacoesMudancaPlanos()
    {
        return $this->hasMany(SolicitacaoMudancaPlano::class);
    }
}