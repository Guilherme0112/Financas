<?php

namespace App\Models;

use App\Enums\CategoriaSaida;
use Illuminate\Database\Eloquent\Model;

class LimiteCategoria extends Model
{
    protected $fillable = [
        'categoria_saida',
        'limite',
        'mes_referencia',
        'notificar_ao_atingir',
        'user_id',
        'recorrente'
    ];

    protected $casts = [
        'categoria_saida' => CategoriaSaida::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
