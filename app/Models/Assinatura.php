<?php

namespace App\Models;

use App\Enums\StatusAssinatura;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assinatura extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',
        'plano_id',
        'data_inicio',
        'data_fim',
        'data_proxima_cobranca',
        'status',
    ];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
        'data_proxima_cobranca' => 'datetime',
        'status' => StatusAssinatura::class
    ];

    public function calcularProximoVencimento(): Carbon
    {
        $hoje = now();
        $vencimentoAtual = $this->data_proxima_cobranca ?? $hoje;
        $dataBase = $vencimentoAtual->gt($hoje) ? $vencimentoAtual : $hoje;
        return $dataBase->copy()->addMonth();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function faturas()
    {
        return $this->hasMany(Fatura::class);
    }

    public function plano()
    {
        return $this->belongsTo(Plano::class);
    }
}
