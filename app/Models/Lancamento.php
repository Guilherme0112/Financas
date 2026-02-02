<?php

namespace App\Models;

use App\Enums\CategoriaEntrada;
use App\Enums\CategoriaSaida;
use App\Enums\TipoValor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lancamento extends Model
{
    use HasFactory;

    protected $appends = ['categoria_label'];

    protected $fillable = [
        'nome',
        'descricao',
        'valor',
        'tipo',
        'recorrente',
        'mes_referencia',
        'categoria_entrada',
        'categoria_saida',
        'foi_pago'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'recorrente' => 'boolean',
        'foi_pago' => 'boolean',
        'tipo' => TipoValor::class,
        'categoria_entrada' => CategoriaEntrada::class,
        'categoria_saida' => CategoriaSaida::class,
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function getCategoriaLabelAttribute(): ?string
    {
        return $this->tipo === TipoValor::SAIDA
            ? $this->categoria_saida?->label()
            : $this->categoria_entrada?->label();
    }

}
