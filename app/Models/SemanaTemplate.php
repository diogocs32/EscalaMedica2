<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SemanaTemplate extends Model
{
    protected $table = 'semanas_template';

    protected $fillable = [
        'escala_padrao_id',
        'numero_semana',
        'nome',
        'observacoes',
    ];

    /**
     * Escala padrão pai
     */
    public function escalaPadrao(): BelongsTo
    {
        return $this->belongsTo(EscalaPadrao::class);
    }

    /**
     * 7 dias desta semana
     */
    public function dias(): HasMany
    {
        return $this->hasMany(DiaTemplate::class);
    }
}
