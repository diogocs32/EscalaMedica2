<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Alocacao extends Model
{
    use HasFactory;

    protected $table = 'alocacoes';

    protected $fillable = [
        'plantonista_id',
        'vaga_id',
        'data_plantao',
        'data_hora_inicio',
        'data_hora_fim',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'data_plantao' => 'date',
        'data_hora_inicio' => 'datetime',
        'data_hora_fim' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: Uma alocação pertence a um plantonista
     */
    public function plantonista(): BelongsTo
    {
        return $this->belongsTo(Plantonista::class);
    }

    /**
     * Relacionamento: Uma alocação pertence a uma vaga
     */
    public function vaga(): BelongsTo
    {
        return $this->belongsTo(Vaga::class);
    }

    /**
     * Scope para alocações ativas
     */
    public function scopeAtivo($query)
    {
        return $query->whereIn('status', ['agendado', 'em_andamento']);
    }

    /**
     * Scope para alocações em um período específico
     */
    public function scopeNoPeriodo($query, $inicio, $fim)
    {
        return $query->where('data_hora_inicio', '<', $fim)
            ->where('data_hora_fim', '>', $inicio);
    }

    /**
     * Verifica se há conflito com outro horário
     */
    public function temConflitoCom($novoInicio, $novoFim): bool
    {
        return $this->data_hora_inicio < $novoFim && $this->data_hora_fim > $novoInicio;
    }

    /**
     * Accessor para duração do plantão em horas
     */
    public function getDuracaoHorasAttribute(): float
    {
        return $this->data_hora_inicio->diffInHours($this->data_hora_fim);
    }
}
