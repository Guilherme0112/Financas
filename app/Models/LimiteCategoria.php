<?php

namespace App\Models;

use App\Enums\CategoriaSaida;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LimiteCategoria extends Model
{
    use HasFactory;

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

    protected $appends = [
        "soma_categoria"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSomaCategoriaAttribute(): float
    {
        $data = Carbon::parse($this->mes_referencia);
        return Lancamento::query()
            ->where('user_id', $this->user_id)
            ->where('categoria_saida', $this->categoria_saida)
            ->whereYear('mes_referencia', $data->year)
            ->whereMonth('mes_referencia', $data->month)
            ->sum('valor');
    }
}
