<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LancamentosImportados extends Model
{
    protected $table = 'lancamentos_importados';

    protected $fillable = [
        'user_id',
        'status',
        'quando_importou',
        'tipo',
        'path',
        'arquivo_nome',
        'total_linhas',
        'erro_mensagem'
    ];

    protected $casts = [
        'status' => 'string',
        'quando_importou' => 'datetime',
        'total_linhas' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
