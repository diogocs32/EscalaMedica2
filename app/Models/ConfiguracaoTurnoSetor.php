<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConfiguracaoTurnoSetor extends Model
{
    protected $table = 'configuracoes_turno_setor';

    protected $fillable = [
        'dia_template_id',
        'turno_id',
        'setor_id',
        'quantidade_necessaria',
        'observacoes',
        'status',
    ];

    protected $casts = [
        'quantidade_necessaria' => 'integer',
    ];

    /**
     * Dia template pai
     */
    public function diaTemplate(): BelongsTo
    {
        return $this->belongsTo(DiaTemplate::class);
    }

    /**
     * Turno configurado
     */
    public function turno(): BelongsTo
    {
        return $this->belongsTo(Turno::class);
    }

    /**
     * Setor configurado
     */
    public function setor(): BelongsTo
    {
        return $this->belongsTo(Setor::class);
    }
}
