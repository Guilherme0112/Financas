<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    protected $fillable = [
        "user_id",
        "nome",
        "valor_objetivo",
        "ate_quando"
    ];
    

    public function lancamentos()
    {
        return $this->hasMany(Lancamento::class, 'meta_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function obterSomaLancamentosAttribute()
    {
        return $this->lancamentos->sum("valor");
    }
}
