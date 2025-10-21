<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Turno extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'hora_inicio',
        'hora_fim',
        'duracao_horas',
        'periodo',
        'status',
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i:s',
        'hora_fim' => 'datetime:H:i:s',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: Um turno pode ter muitas vagas
     */
    public function vagas(): HasMany
    {
        return $this->hasMany(Vaga::class);
    }

    /**
     * Scope para turnos ativos
     */
    public function scopeAtivo($query)
    {
        return $query->where('status', 'ativo');
    }

    /**
     * Verifica se é um turno que atravessa a meia-noite
     */
    public function isCorujao(): bool
    {
        return Carbon::parse($this->hora_fim)->lessThanOrEqualTo(Carbon::parse($this->hora_inicio));
    }

    /**
     * Accessor para exibir o horário formatado
     */
    public function getHorarioFormatadoAttribute(): string
    {
        return Carbon::parse($this->hora_inicio)->format('H:i') . ' - ' . Carbon::parse($this->hora_fim)->format('H:i');
    }
}
