<?php

namespace App\Models;

use App\Enums\Planos;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plano extends Model
{

    use HasFactory;

    protected $fillable = [
        'nome',
        'plano',
        'descricao',
        'preco',
    ];

    protected $casts = [
        "plano" => Planos::class,
        'preco' => 'decimal:2',
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }
}
