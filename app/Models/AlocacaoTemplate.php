<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlocacaoTemplate extends Model
{
    protected $table = 'alocacoes_template';

    protected $fillable = [
        'escala_padrao_id',
        'semana',
        'dia',
        'turno_id',
        'setor_id',
        'slot',
        'plantonista_id',
    ];

    protected $casts = [
        'semana' => 'integer',
        'dia' => 'integer',
        'slot' => 'integer',
    ];

    public function escalaPadrao(): BelongsTo
    {
        return $this->belongsTo(EscalaPadrao::class);
    }

    public function turno(): BelongsTo
    {
        return $this->belongsTo(Turno::class);
    }

    public function setor(): BelongsTo
    {
        return $this->belongsTo(Setor::class);
    }

    public function plantonista(): BelongsTo
    {
        return $this->belongsTo(Plantonista::class);
    }
}
