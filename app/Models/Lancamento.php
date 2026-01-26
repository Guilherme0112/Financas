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

    protected $fillable = [
        'nome',
        'descricao',
        'valor',
        'tipo',
        'recorrente',
        'mes_referencia',
        'categoria_entrada',
        'categoria_saida',
    ];

    protected $casts = [
        'valor' => 'float',
        'recorrente' => 'boolean',
        'tipo' => TipoValor::class, 
        'categoria_entrada' => CategoriaEntrada::class,
        'categoria_saida' => CategoriaSaida::class,
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
