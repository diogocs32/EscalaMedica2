<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vaga extends Model
{
    use HasFactory;

    protected $fillable = [
        'unidade_id',
        'setor_id',
        'turno_id',
        'quantidade_necessaria',
        'observacoes',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: Uma vaga pertence a uma unidade
     */
    public function unidade(): BelongsTo
    {
        return $this->belongsTo(Unidade::class);
    }

    /**
     * Relacionamento: Uma vaga pertence a um setor
     */
    public function setor(): BelongsTo
    {
        return $this->belongsTo(Setor::class);
    }

    /**
     * Relacionamento: Uma vaga pertence a um turno
     */
    public function turno(): BelongsTo
    {
        return $this->belongsTo(Turno::class);
    }

    /**
     * Relacionamento: Uma vaga pode ter muitas alocações
     */
    public function alocacoes(): HasMany
    {
        return $this->hasMany(Alocacao::class);
    }

    /**
     * Scope para vagas ativas
     */
    public function scopeAtivo($query)
    {
        return $query->where('status', 'ativo');
    }

    /**
     * Accessor para descrição completa da vaga
     */
    public function getDescricaoCompletaAttribute(): string
    {
        return "{$this->unidade->nome} - {$this->setor->nome} - {$this->turno->nome}";
    }
}
