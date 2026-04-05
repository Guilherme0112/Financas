<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessamentoFaturaErro extends Model
{
    protected $table = 'processamento_faturas_erros';

    protected $fillable = [
        'assinatura_id',
        'etapa',
        'mensagem_erro',
        'stack_trace',
        'payload',
        'resolvido_em'
    ];

    protected $casts = [
        'payload' => 'array',
        'resolvido_em' => 'datetime',
    ];

    public function assinatura(): BelongsTo
    {
        return $this->belongsTo(Assinatura::class);
    }

    public function scopePendentes($query)
    {
        return $query->whereNull('resolvido_em');
    }
}