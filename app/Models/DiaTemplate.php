<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiaTemplate extends Model
{
    protected $table = 'dias_template';

    protected $fillable = [
        'semana_template_id',
        'dia_semana',
        'observacoes',
    ];

    /**
     * Semana pai
     */
    public function semanaTemplate(): BelongsTo
    {
        return $this->belongsTo(SemanaTemplate::class);
    }

    /**
     * Configurações de turno+setor+vagas para este dia
     */
    public function configuracoes(): HasMany
    {
        return $this->hasMany(ConfiguracaoTurnoSetor::class);
    }
}
