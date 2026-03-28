<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitacaoMudancaPlano extends Model
{
    protected $table = 'solicitacoes_mudanca_plano';

    protected $fillable = [
        'user_id',
        'assinatura_id',
        'fatura_id',
        'plano_antigo_id',
        'plano_novo_id',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Relacionamento com o Usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assinatura(): BelongsTo
    {
        return $this->belongsTo(Assinatura::class);
    }

    public function fatura(): BelongsTo
    {
        return $this->belongsTo(Fatura::class);
    }

    public function planoAntigo(): BelongsTo
    {
        return $this->belongsTo(Plano::class, 'plano_antigo_id');
    }

    public function planoNovo(): BelongsTo
    {
        return $this->belongsTo(Plano::class, 'plano_novo_id');
    }
}
