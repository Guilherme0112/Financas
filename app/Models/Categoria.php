<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{

    use HasFactory;
    protected $fillable = ['nome', 'tipo'];

    public function lancamentos()
    {
        return $this->hasMany(Lancamento::class);
    }
}
