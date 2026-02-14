<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LancamentosExportados extends Model
{
    use HasFactory;

    protected $table = 'lancamentos_exportados';

    protected $fillable = [
        'user_id',
        'status',
        'tipo',
        'filename',
        'error',
    ];

    protected $casts = [
        'status' => 'string',
        'tipo' => 'string',
    ];

    /**
     * Relação com usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helpers opcionais (melhora legibilidade)
     */
    public function isConcluido(): bool
    {
        return $this->status === 'concluido';
    }

    public function isFalhou(): bool
    {
        return $this->status === 'falhou';
    }

    public function isProcessando(): bool
    {
        return in_array($this->status, ['iniciado', 'pendente']);
    }
}
